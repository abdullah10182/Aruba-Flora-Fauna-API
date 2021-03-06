<?php

/**
 * @file
 * Contains \Drupal\aff_api\Controller\GetDashboardAdmins.
 */

namespace Drupal\aff_api\Controller;

use Drupal\Core\Controller\ControllerBase;


use  Drupal\user\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SafeMarkup;

/**
 * Controller routines for weblab_dashboard routes.
 */
class GetDashboardAdmins extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_dashboard_admins( Request $request ) {
    
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $authorized = false;

    foreach ($roles as $key => $value) {
      //print $value;
      if($value == 'dashboard_admin' || $value == 'administrator'){
        $authorized = true;
        break;
      }else {
        $authorized = false;
      }
    }

    if(!$authorized){
      return new Response('permission denied');
    }

    $response_array= [];

    $ids = \Drupal::entityQuery('user')
    ->condition('status', 1)
    ->condition('roles', 'dashboard_admin')
    ->execute();
    $users = User::loadMultiple($ids);

    foreach ($users as $user) {
      $response_array[] = [
        'uid' => $user->uid->value,
        'email' => $user->mail->value,
        'phone' => $user->field_phone_number->value,
        'name' => $user->field_name->value . ' ' . $user->field_last_name->value
      ];
    }

    //print_r(json_encode(array_values($users)));die;
    $response = new Response(json_encode($response_array));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
    
    // dpm($users);
  }
}//end class

