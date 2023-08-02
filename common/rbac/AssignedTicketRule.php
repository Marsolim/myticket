<?php

namespace common\rbac;

use yii\rbac\Rule;
use common\models\Ticket;
use common\models\Progress;

/**
 * Checks if authorID matches user passed via params
 */
class AssignedTicketRule extends Rule
{
    public $name = 'isAssignedTicket';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['ticket']) ? (!isset($params['ticket']->engineer_id) || ($params['ticket']->engineer_id == $user)) : false;
    }
}