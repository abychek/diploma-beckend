<?php

namespace ProjectsBundle\Controller;


use AppBundle\Controller\RestController;
use ProjectsBundle\Entity\Member;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MembersController
 * @package ProjectsBundle\Controller
 * @Route("/projects/{projectId}/members")
 */
class MembersController extends RestController
{

    /**
     * @Route("/")
     * @Method({"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        if ($request->get('projectId') && $project = $this->getProjectById($request->get('projectId'))) {
            $result = [];
            $options = $this->handlePagination($request);
            $members = $this->getDoctrine()->getRepository('ProjectsBundle:Member')->getByProject($project, $options);
            foreach ($members as $member) {
                $result[] = $member->toArray();
            }

            return new JsonResponse($result);
        }

        return $this->generateInfoResponse(JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/{id}/")
     * @Method({"GET"})
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getAction(Request $request, $id)
    {
        if ($member = $this->getDoctrine()->getRepository('ProjectsBundle:Member')->find($id)) {
            return new JsonResponse($member->toArray());
        }

        return $this->generateInfoResponse(JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/")
     * @Method({"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        if ($request->get('projectId') && $project = $this->getProjectById($request->get('projectId'))) {
            if (
                $request->get('employeeId') &&
                $request->get('roleId') &&
                ($employee = $this->getDoctrine()->getRepository('StaffBundle:Employee')->find($request->get('employeeId'))) &&
                ($role = $this->getDoctrine()->getRepository('ProjectsBundle:ProjectRole')->find($request->get('roleId')))
            ) {
                $em = $this->getDoctrine()->getManager();
                $em->persist((new Member())
                    ->setEmployee($employee)
                    ->setRole($role)
                    ->setProject($project)
                    ->setStatus(Member::STATUS_AVAILABLE));
                $em->flush();

                return $this->generateInfoResponse(JsonResponse::HTTP_CREATED);
            }

            return $this->generateInfoResponse(JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->generateInfoResponse(JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateAction(Request $request, $id)
    {
        // TODO: Implement updateAction() method.
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        // TODO: Implement deleteAction() method.
    }

    private function getProjectById($id)
    {
        return $this->getDoctrine()->getRepository('ProjectsBundle:Project')->find($id);
    }
}