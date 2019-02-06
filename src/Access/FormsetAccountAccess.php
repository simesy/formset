<?php

namespace Drupal\formset\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the custom access control handler for the user accounts.
 */
class FormsetAccountAccess {

  /**
   * Check whether the user has 'administer formset' or 'administer formset submission' permission.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public static function checkAdminAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermissions($account, ['administer formset']);
  }

  /**
   * Check whether the user has 'administer formset' etc.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public static function checkEditAccess(AccountInterface $account) {
    // @todo include "edit own formset".
    return AccessResult::allowedIfHasPermissions($account, ['administer formset', 'Edit any form set'], 'OR');
  }

  /**
   * Check whether the user has 'administer' or 'overview' permission.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public static function checkOverviewAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermissions($account, ['administer formset', 'access formset overview'], 'OR');
  }

}
