<?php

namespace Travel\TravelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\SolariumBundle\DependencyInjection\NelmioSolariumExtension;

class DefaultController extends Controller
{
	public function indexAction()
	{

	//FACETTE - TOUS LES VOYAGES - Return destination offers + number of offers available
		$client = $this->get('solarium.client');
		$query = $client->createSelect();
		$query->createFilterQuery('inclus')->setQuery('description:*inclus* AND terms:[* TO *]'); // [* TO *] means "not empty"
		$facetSet = $query->getFacetSet();
		$facetSet->createFacetField('pays')->setField('manufacturer')->setMincount(1);
		$resultset = $client->select($query);
		$facet = $resultset->getFacetSet()->getFacet('pays');

		return $this->render('TravelTravelBundle:Default:index.html.twig', array(
			'facet' => $facet,
			));
	}

        public function sort_resorts_by_countryAction($pays,$start)
        {
                // Requete
                $my_query = 'manufacturer:'.$pays.' AND description:*inclus* AND terms:[* TO *]';

                // Create new solarium instance
                $client = $this->get('solarium.client');
                $select = $client->createSelect();
                $select->setQuery($my_query);
                $select->addSort('price', $select::SORT_ASC);
                $select->setRows(1500);
                if (isset($start))
                {
                        $select->setStart($start);
                }
                $resultsolr = $client->select($select);
                $nb_result_pays =  $resultsolr->getNumFound();

                // Nav facettes
                $query = $client->createSelect();
                $query->createFilterQuery('inclus')->setQuery('description:*inclus* AND terms:[* TO *]');
                $facetSet = $query->getFacetSet();
                $facetSet->createFacetField('pays')->setField('manufacturer')->setMincount(1);
                $resultset = $client->select($query);
                $facet = $resultset->getFacetSet()->getFacet('pays');

                /* Pagination
                $paginator  = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                        $resultsolr,
                        $this->get('request')->query->get('page', 1),10);

                */
                return $this->render('TravelTravelBundle:Default:sejours_par_pays.html.twig',
                                array('resultsolr' => $resultsolr,
                                        'pays' => $pays,
                                        'facet' => $facet,
                                        'nb_result_pays' => $nb_result_pays/*,
                                        'pagination' => $pagination*/));
        }








}
