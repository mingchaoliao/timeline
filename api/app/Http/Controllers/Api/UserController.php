<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HttpException;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Client
     */
    private $httpClient;
    /**
     * @var AccessTokenController
     */
    private $accessTokenController;

    public function __construct(
        UserRepository $userRepository,
        Client $httpClient,
        AccessTokenController $accessTokenController
    ) {
        $this->userRepository = $userRepository;
        $this->httpClient = $httpClient;
        $this->accessTokenController = $accessTokenController;
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'string',
            'email' => 'email',
            'password' => 'string'
        ]);

        try {
            $user = $this->userRepository->createUser(
                $request->get('name'),
                $request->get('email'),
                $request->get('password'),
                false
            );

            $token = $this->getToken(
                'WebPasswordGrantClient',
                $request->get('email'),
                $request->get('password'),
                $request
            );

            return response()->json(array_merge($user->toArray(), $token));
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (BadResponseException $e) {
            return response()->json(
                json_decode($e->getResponse()->getBody()->getContents()),
                $e->getResponse()->getStatusCode()
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getCurrentUser()
    {

        $user = $this->userRepository->getCurrentUser();

        return response()->json($user);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
            $token = $this->getToken(
                'WebPasswordGrantClient',
                $request->get('email'),
                $request->get('password'),
                $request
            );

            $user = $this->userRepository->getByEmail($request->get('email'));

            $userArray = $user->toArray();

            return response()->json(array_merge($userArray, $token));
        } catch (HttpException $e) {
            if ($e->isJson()) {
                return response()->json(json_decode($e->getMessage(), true),
                    $e->getCode());
            }

            return response()->json(['message' => $e->getMessage()], $e->getCode());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param string $oauthClientName
     * @param string $email
     * @param string $password
     *
     *
     * @param Request $request
     *
     * @return array
     * @throws HttpException
     * @throws \Exception
     */
    private function getToken(
        string $oauthClientName,
        string $email,
        string $password,
        Request $request
    ): array {
        $oauthClient = DB::table('oauth_clients')->select([
            'id',
            'secret'
        ])->where('name', $oauthClientName)->first();

        if ($oauthClient === null) {
            throw new \Exception('Invalid oauth client');
        }

        $requestTokenData = [
            'grant_type' => 'password',
            'client_id' => $oauthClient->id,
            'client_secret' => $oauthClient->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '',
        ];

        $request->request->add($requestTokenData);

        /**
         * @var Response $response
         * */
        $response = App::call('Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');

        if ($response->getStatusCode() >= 400) {
            throw HttpException::withJsonMessage($response->getContent(),
                $response->getStatusCode());
        }

        $content = json_decode($response->getContent(), true);

        return [
            'tokenType' => $content['token_type'],
            'accessToken' => $content['access_token']
        ];
    }
}

