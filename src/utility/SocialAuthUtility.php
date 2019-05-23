<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\socialauth\utility
 * @category   CategoryName
 */

namespace lispa\amos\socialauth\utility;

use lispa\amos\admin\models\UserProfile;

/**
 * Class SocialAuthUtility
 * @package lispa\amos\socialauth\utility
 */
class SocialAuthUtility
{
    /**
     * This method takes an array of UserProfile objects and return an array with user id in the key and name, surname and email in the value.
     * @param UserProfile[] $userProfiles
     * @return array
     */
    public static function makeUsersByCFReadyForSelect($userProfiles)
    {
        $readyForSelect = [];
        foreach ($userProfiles as $userProfile) {
            $readyForSelect[$userProfile->user_id] = $userProfile->getNomeCognome() . ' (' . $userProfile->user->email . ')';
        }
        return $readyForSelect;
    }

    /**
     * This method takes an array of UserProfile objects and return an array with user id in the key and name, surname and email in the value.
     * @param UserProfile[] $userProfiles
     * @return array
     */
    public static function makeUsersByCFUserIds($userProfiles)
    {
        $userIds = [];
        foreach ($userProfiles as $userProfile) {
            $userIds[$userProfile->user_id] = $userProfile->user_id;
        }
        return $userIds;
    }
}
