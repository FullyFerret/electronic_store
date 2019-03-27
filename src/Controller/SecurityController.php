<?php

namespace App\Controller;

use Junker\Symfony\JSendFailResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Response;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;


class SecurityController extends AbstractFOSRestController
{
    private $client_manager;

    public function __construct(ClientManagerInterface $client_manager)
    {
        $this->client_manager = $client_manager;
    }

    /**
     * Create Client.
     * @FOSRest\Post("/create-client")
     *
     * @return Response
     */
    public function authenticationAction(Request $request, LoggerInterface $logger)
    {
        $logger->info("Entering authenticationAction...");
        $data = json_decode($request->getContent(), true);

        if (empty($data['redirect-uri']) || empty($data['grant-type'])) {
            $logger->notice("Missing redirect-uri or grant-type from POST content.");
            return new JSendFailResponse("Missing redirect-uri or grant-type from POST content.", Response::HTTP_BAD_REQUEST);
        }

        $clientManager = $this->client_manager;

        $logger->info("Creating client...");
        $client = $clientManager->createClient();
        $client->setRedirectUris([$data['redirect-uri']]);
        $client->setAllowedGrantTypes([$data['grant-type']]);
        $clientManager->updateClient($client);

        $logger->info("Updated client.");

        $rows = [
            'client_id' => $client->getPublicId(), 'client_secret' => $client->getSecret()
        ];
        return $this->handleView($this->view($rows));
    }
}