<?php
/**
* PHPCI - Continuous Integration for PHP
*
* @copyright    Copyright 2013, Block 8 Limited.
* @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
* @link         http://www.phptesting.org/
*/

namespace PHPCI\Model\Build;

use PHPCI\Model\Build\RemoteGitBuild;

/**
* Github Build Model
* @author       Dan Cryer <dan@block8.co.uk>
* @package      PHPCI
* @subpackage   Core
*/
class GithubBuild extends RemoteGitBuild
{
    /**
    * Get link to commit from another source (i.e. Github)
    */
    public function getCommitLink()
    {
        return 'https://github.com/' . $this->getProject()->getReference() . '/commit/' . $this->getCommitId();
    }

    /**
    * Get link to branch from another source (i.e. Github)
    */
    public function getBranchLink()
    {
        return 'https://github.com/' . $this->getProject()->getReference() . '/tree/' . $this->getBranch();
    }

    /**
    * Send status updates to any relevant third parties (i.e. Github)
    */
    public function sendStatusPostback()
    {
        $project    = $this->getProject();

        // The postback will only work if we have an access token.
        if (!$project->getToken()) {
            return;
        }

        $url    = 'https://api.github.com/repos/'.$project->getReference().'/statuses/'.$this->getCommitId();
        $http   = new \b8\HttpClient();

        switch($this->getStatus())
        {
            case 0:
            case 1:
                $status = 'pending';
                break;
            case 2:
                $status = 'success';
                break;
            case 3:
                $status = 'failure';
                break;
            default:
                $status = 'error';
                break;
        }

        $url = \b8\Config::getInstance()->get('phpci.url');
        $params = array(    'state' => $status,
                            'target_url' => $url . '/build/view/' . $this->getId());
        $headers = array(
            'Authorization: token ' . $project->getToken(),
            'Content-Type: application/x-www-form-urlencoded'
            );

        $http->setHeaders($headers);
        $http->request('POST', $url, json_encode($params));
    }

    /**
    * Get the URL to be used to clone this remote repository.
    */
    protected function getCloneUrl()
    {
        $key = trim($this->getProject()->getGitKey());

        if (!empty($key)) {
            return 'git@github.com:' . $this->getProject()->getReference() . '.git';
        } else {
            return 'https://github.com/' . $this->getProject()->getReference() . '.git';
        }
    }
}
