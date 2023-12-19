<?php

namespace Drupal\ecc_devolved_editor\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Redirect.
 */
class Redirect implements HttpKernelInterface {
  /**
   * The HTTP kernel.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * The redirect response.
   *
   * @var \Symfony\Component\HttpFoundation\RedirectResponse|null
   */
  protected $redirectResponse;

  /**
   * Constructs a new Redirect object.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The HTTP kernel.
   */
  public function __construct(HttpKernelInterface $http_kernel) {
    $this->httpKernel = $http_kernel;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MAIN_REQUEST, $catch = true): Response {
    $response = $this->httpKernel->handle($request, $type, $catch);
    return $this->redirectResponse ?: $response;
  }

  /**
   * Sets the redirect response.
   *
   * @param \Symfony\Component\HttpFoundation\RedirectResponse|null $redirectResponse
   *   The redirect response.
   */
  public function setRedirectResponse(?RedirectResponse $redirectResponse) {
    $this->redirectResponse = $redirectResponse;
  }

}
