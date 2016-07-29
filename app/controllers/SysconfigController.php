<?php
/**
 * app/controllers/SysconfigController.php
 *
 * @author Nicolas CARPi <nicolas.carpi@curie.fr>
 * @copyright 2012 Nicolas CARPi
 * @see http://www.elabftw.net Official website
 * @license AGPL-3.0
 * @package elabftw
 */
namespace Elabftw\Elabftw;

use Exception;

/**
 * Deal with ajax requests sent from the sysconfig page or full form from sysconfig.php
 *
 */
try {
    require_once '../../app/common.inc.php';

    if (!$_SESSION['is_sysadmin']) {
        throw new Exception('Non sysadmin user tried to access sysadmin panel.');
    }

    $tab = '1';
    $redirect = false;

    $Teams = new Teams();

    // PROMOTE SYSADMIN
    if (isset($_POST['promoteSysadmin'])) {
        $Users = new Users();
        if ($Users->promoteSysadmin($_POST['email'])) {
            echo '1';
        } else {
            echo '0';
        }
    }

    // CREATE TEAM
    if (isset($_POST['teamsCreate'])) {
        if ($Teams->create($_POST['teamsName'])) {
            echo '1';
        } else {
            echo '0';
        }
    }

    // UPDATE TEAM NAME
    if (isset($_POST['teamsUpdate'])) {
        if ($Teams->updateName($_POST['teamsUpdateId'], $_POST['teamsUpdateName'])) {
            echo '1';
        } else {
            echo '0';
        }
    }

    // DESTROY TEAM
    if (isset($_POST['teamsDestroy'])) {
        if ($Teams->destroy($_POST['teamsDestroyId'])) {
            echo '1';
        } else {
            echo '0';
        }
    }

    // SEND TEST EMAIL
    if (isset($_POST['testemailSend'])) {
        $Sysconfig = new Sysconfig();
        if ($Sysconfig->testemailSend($_POST['testemailEmail'])) {
            echo '1';
        } else {
            echo '0';
        }
    }

    // SEND MASS EMAIL
    if (isset($_POST['massEmail'])) {
        $Sysconfig = new Sysconfig();
        if ($Sysconfig->massEmail($_POST['subject'], $_POST['body'])) {
            echo '1';
        } else {
            echo '0';
        }
    }

    // DESTROY LOGS
    if (isset($_POST['logsDestroy'])) {
        $Logs = new Logs();
        if ($Logs->destroy()) {
            echo '1';
        } else {
            echo '0';
        }
    }

    // TAB 2 to 5
    if (isset($_POST['updateConfig'])) {
        $redirect = true;

        if (isset($_POST['lang'])) {
            $tab = '2';
        }

        if (isset($_POST['stampshare'])) {
            $tab = '3';
        }

        if (isset($_POST['admin_validate'])) {
            $tab = '4';
        }

        if (isset($_POST['mail_method'])) {
            $tab = '5';
        }

        if (!update_config($_POST)) {
            throw new Exception('Error updating config');
        }

    }

    $_SESSION['ok'][] = _('Configuration updated successfully.');

} catch (Exception $e) {
    $Logs = new Logs();
    $Logs->create('Error', $_SESSION['userid'], $e->getMessage());
    // we can show error message to sysadmin
    $_SESSION['ko'][] = $e->getMessage();
} finally {
    if ($redirect) {
        header('Location: ../../sysconfig.php?tab=' . $tab);
    }
}