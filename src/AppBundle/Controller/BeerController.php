<?php

namespace AppBundle\Controller;

use AppBundle\Form\BeerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Beer;

/**
 * @Route("/beers")
 */
class BeerController extends Controller
{
	/**
	 * @Route("/")
	 * @Method("GET")
	 */
	public function indexAction()
	{
		$beers = $this->getDoctrine()
					  ->getRepository( 'AppBundle:Beer' )
				      ->findAll();

		$beers = $this->get('jms_serializer')->serialize($beers, 'json');

		return new Response($beers);
	}

	/**
	 * @Route("/{id}")
	 * @Method("GET")
	 */
	public function getAction(Beer $id)
	{
		$beer = $this->get('jms_serializer')->serialize($id, 'json');

		return new Response($beer);
	}

	/**
	 * @Route("/")
	 * @Method("POST")
	 */
	public function saveAction(Request $request)
	{
		$data = $request->getContent();
		parse_str($data, $data_arr);

		$beer = new Beer();
		$form = $this->createForm(BeerType::class, $beer);
		$form->submit($data_arr);

		$doctrine = $this->getDoctrine()->getManager();

		$doctrine->persist($beer);
		$doctrine->flush();

		return new JsonResponse(['msg' => 'Cerveja criada com sucesso!'], 200);
	}

	/**
	 * @Route("/")
	 * @Method("PUT")
	 */
	public function updateAction(Request $request)
	{
		$data = $request->getContent();
		parse_str($data, $data_arr);

		$beer = $this->getDoctrine()
					 ->getRepository('AppBundle:Beer')
					 ->find($data_arr['id']);

		if(!$beer) {
			return new JsonResponse(['msg' => 'Cerveja não encontrada!'], 404);
		}

		$form = $this->createForm(BeerType::class, $beer);
		$form->submit($data_arr);

		$doctrine = $this->getDoctrine()->getManager();

		$doctrine->merge($beer);
		$doctrine->flush();

		return new JsonResponse(['msg' => 'Cerveja atualizada com sucesso!'], 200);
	}

	/**
	 * @Route("/{id}")
	 * @Method("DELETE")
	 */
	public function deleteAction(Beer $id)
	{
		$doctrine = $this->getDoctrine()->getManager();
		$doctrine->remove($id);
		$doctrine->flush();

		return new JsonResponse(['msg' => 'Cerveja removida com sucesso!'], 200);
	}
}
