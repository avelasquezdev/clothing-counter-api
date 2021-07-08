<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\User;
use App\Repository\UserRepository;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @param Request                  $request
     * @param JWTTokenManagerInterface $jwt
     * @param UserRepository           $userRepository
     * @param EventDispatcherInterface $dispatcher
     *
     * @return JWTAuthenticationFailureResponse|JsonResponse
     */
    public function googleLogin(
        Request $request,
        JWTTokenManagerInterface $jwt,
        UserRepository $userRepository
//        EventDispatcherInterface $dispatcher
    ) {
        $body = $request->getContent();
        $body = \GuzzleHttp\json_decode($body, true);
        $client = new \Google_Client(['client_id' => $_ENV['GOOGLE_API_KEY']]);
        $payload = $client->verifyIdToken($body['id_token']);
        if ($payload) {
            $userid = $payload['sub'];
            // If request specified a G Suite domain:
            //$domain = $payload['hd'];

            $email = $payload['email'];
            $role = $body['role'];
            $user = $userRepository->loadUserByEmail($email);
            if (null === $user || !$user instanceof UserInterface) {
                $user = new User();
                $user->setGoogleID($userid);
//                $this->setUserRegistrationInfo($user, $payload['given_name'], $payload['family_name'], $email);
                $this->setUserRegistrationInfo($user, $email, $role);
                $this->save($user);

//                $userEvent = new UserRegistrationEvent($user);
//                $dispatcher->dispatch('user.registration', $userEvent);
            } elseif ($user instanceof User) {
                $user->setGoogleID($userid);
                $user->setEmail($user->getEmail() ?: $payload['email']);
//                $user->setSurname($user->getSurname() ?: $payload['family_name']);

                $this->save($user);
            }

            if (null !== $user) {
                return new JsonResponse($userRepository->toLoginObject(['token' => $jwt->create($user)], $user));
            }
        }

        return new JWTAuthenticationFailureResponse();
    }

    private function setUserRegistrationInfo(
        User $user,
        string $email,
        string $username,
        string $role,
        string $password = null
    ): void {
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles([$role]);
        $user->setPassword($password ?: md5(uniqid()));
    }

    private function save($user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * @param Request                  $request
     * @param JWTTokenManagerInterface $jwt
     * @param UserRepository           $userRepository
     * @param EventDispatcherInterface $dispatcher
     *
     * @return JWTAuthenticationFailureResponse|JsonResponse
     */
    public function facebookLogin(
        Request $request,
        JWTTokenManagerInterface $jwt,
        UserRepository $userRepository
//        EventDispatcherInterface $dispatcher
    ) {
        $body = $request->getContent();
        $body = \GuzzleHttp\json_decode($body, true);
        try {
            $authToken = $body['authToken'] ?? null;
            $fb = new Facebook(
                [
                    'app_id' => $_ENV['FACEBOOK_APP_ID'],
                    'app_secret' => $_ENV['FACEBOOK_APP_SECRET'],
                    'default_graph_version' => 'v2.10',
                    'default_access_token' => $authToken, // optional
                ]
            );
        } catch (FacebookSDKException $e) {
            return new JWTAuthenticationFailureResponse($e->getMessage());
        }

        try {
            $response = $fb->get('/me?fields=id,first_name,last_name,email', $authToken);
        } catch (FacebookResponseException $e) {
            return new JWTAuthenticationFailureResponse($e->getMessage());
        } catch (FacebookSDKException $e) {
            return new JWTAuthenticationFailureResponse($e->getMessage());
        }

        try {
            $socialUser = $response->getGraphUser();
        } catch (FacebookSDKException $e) {
            return new JWTAuthenticationFailureResponse($e->getMessage());
        }
        if ($socialUser->getId()) {
            $userid = $socialUser->getId();
            // If request specified a G Suite domain:
            //$domain = $payload['hd'];
            $currentEmail = $body['email'];
            $email = $socialUser->getEmail();
            $role = $body['role'];
//            $first_name = $socialUser->getFirstName();
//            $last_name = $socialUser->getLastName();
            $user = $userRepository->loadUserByEmail($currentEmail);

            if (null === $user || !$user instanceof User) {
                $user = new User();

                $user->setFacebookID($userid);
//                $this->setUserRegistrationInfo($user, $first_name, $last_name, $email);
                $this->setUserRegistrationInfo($user, $email, $role);
                $this->save($user);

//                $userEvent = new UserRegistrationEvent($user);
//                $dispatcher->dispatch('user.registration', $userEvent);
            } elseif ($user instanceof UserInterface) {
                $user->setFacebookID($userid);
                $user->setEmail($user->getEmail() ?: $email);
//                $user->setSurname($user->getSurname() ?: $last_name);

                $this->save($user);
            }

            if (null !== $user) {
                return new JsonResponse($userRepository->toLoginObject(['token' => $jwt->create($user)], $user));
            }
        }

        return new JWTAuthenticationFailureResponse();
    }

    /**
     * @param Request                  $request
     * @param JWTTokenManagerInterface $jwt
     * @param UserRepository           $userRepository
     * @param EventDispatcherInterface $dispatcher
     * @param ValidatorInterface       $validator
     *
     * @return JWTAuthenticationFailureResponse|JsonResponse
     */
    public function register(
        Request $request,
        JWTTokenManagerInterface $jwt,
        UserRepository $userRepository,
        EventDispatcherInterface $dispatcher,
        ValidatorInterface $validator
    ) {
        $body = $request->getContent();
        $body = \GuzzleHttp\json_decode($body, true);

        try {
            $user = $userRepository->loadUserByEmail($body['email']);
            if ($user) {
                $roles = $user->getRoles();
                return new JsonResponse(["message"=>"El usuario ya se encuentra registrado como "
                    . str_replace(', USER', '',
                        str_replace('ROLE_', '', implode(', ', $roles)))], 401);
            } else {
                $user = new User();
                $this->setUserRegistrationInfo($user, $body['email'], $body['username'], $body['roles'], $body['password']);
            }

            $this->save($user);
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                throw new ValidationException($errors);
            }
            $this->save($user);

            return new JsonResponse($userRepository->toLoginObject(['token' => $jwt->create($user)], $user));
        } catch (\Exception $e) {
            return new JWTAuthenticationFailureResponse($e->getMessage());
        }
    }
}
