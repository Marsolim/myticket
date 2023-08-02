<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m230706_023236_init_rbac
 */
class m230706_023236_init_rbac extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $auth = Yii::$app->authManager;

        $adminuser = User::findOne(['username' => 'admin']);
        if (empty($adminuser)){
            $adminuser = new User();
            $adminuser->username = 'admin';
            $adminuser->password = 'hardcode123';
            $adminuser->email = $this->email;
            $adminuser->setPassword($this->password);
            $adminuser->generateAuthKey();
            $adminuser->status = User::STATUS_ACTIVE;
            $adminuser->save();
            $adminuser->refresh();
        }

        // add "seeTickets" permission
        $viewTickets = $auth->createPermission('viewTickets');
        $viewTickets->description = 'View tickets in grid/list';
        $auth->add($viewTickets);

        // add "issueTicket" permission
        $issueTicket = $auth->createPermission('issueTicket');
        $issueTicket->description = 'Create a ticket';
        $auth->add($issueTicket);

        // add "closeTicket" permission
        $closeTicket = $auth->createPermission('closeTicket');
        $closeTicket->description = 'Update ticket status to closed';
        $auth->add($closeTicket);

        // add "createTicket" permission
        $manageProgress = $auth->createPermission('manageProgress');
        $manageProgress->description = 'Create a ticket progress and start it.';
        $auth->add($manageProgress);

        // add "createStore" permission
        $manageStore = $auth->createPermission('manageStore');
        $manageStore->description = 'Create a store';
        $auth->add($manageStore);

        // add "closeTicket" permission
        $closeStore = $auth->createPermission('closeStore');
        $closeStore->description = 'Update store status to closed';
        $auth->add($closeStore);

        // add "closeTicket" permission
        $uploadDocument = $auth->createPermission('uploadDocument');
        $uploadDocument->description = 'Upload document to server.';
        $auth->add($uploadDocument);
        
        // add "closeTicket" permission
        $viewTicketSummary = $auth->createPermission('viewTicketSummary');
        $viewTicketSummary->description = 'View ticket summary report.';
        $auth->add($viewTicketSummary);
        
        // add "issueTicket" permission
        $manageUser = $auth->createPermission('manageUser');
        $manageUser->description = 'Manage user by assigning role';
        $auth->add($manageUser);
        
        // add "issueTicket" permission
        $manageRegion = $auth->createPermission('manageRegion');
        $manageRegion->description = 'Manage regional data';
        $auth->add($manageRegion);

        // add "author" role and give this role the "createPost" permission
        $engineer = $auth->createRole('Engineer');
        $auth->add($engineer);
        $auth->addChild($engineer, $viewTickets);
        $auth->addChild($engineer, $manageProgress);
        $auth->addChild($engineer, $uploadDocument);
        
        // add "author" role and give this role the "createPost" permission
        $storeManager = $auth->createRole('Store Manager');
        $auth->add($storeManager);
        $auth->addChild($storeManager, $viewTickets);
        $auth->addChild($storeManager, $issueTicket);
        $auth->addChild($storeManager, $manageStore);
        $auth->addChild($storeManager, $viewTicketSummary);
        $auth->addChild($storeManager, $uploadDocument);

        // add "author" role and give this role the "createPost" permission
        $generalManager = $auth->createRole('General Manager');
        $auth->add($generalManager);
        $auth->addChild($generalManager, $manageRegion);
        $auth->addChild($generalManager, $manageStore);
        $auth->addChild($generalManager, $closeStore);
        $auth->addChild($generalManager, $viewTicketSummary);
        $auth->addChild($generalManager, $uploadDocument);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        $admin = $auth->createRole('Administrator');
        $auth->add($admin);
        $auth->addChild($admin, $viewTickets);
        $auth->addChild($admin, $issueTicket);
        $auth->addChild($admin, $closeTicket);
        $auth->addChild($admin, $manageRegion);
        $auth->addChild($admin, $manageStore);
        $auth->addChild($admin, $closeStore);
        $auth->addChild($admin, $uploadDocument);
        $auth->addChild($admin, $manageUser);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        $sysadmin = $auth->createRole('System Administrator');
        $auth->add($sysadmin);
        $auth->addChild($sysadmin, $viewTickets);
        $auth->addChild($sysadmin, $issueTicket);
        $auth->addChild($sysadmin, $closeTicket);
        $auth->addChild($sysadmin, $manageProgress);
        $auth->addChild($sysadmin, $manageRegion);
        $auth->addChild($sysadmin, $manageStore);
        $auth->addChild($sysadmin, $closeStore);
        $auth->addChild($sysadmin, $viewTicketSummary);
        $auth->addChild($sysadmin, $uploadDocument);
        $auth->addChild($sysadmin, $manageUser);

        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        //$auth->assign($author, 2);
        $auth->assign($sysadmin, $adminuser->id);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
    }
}
