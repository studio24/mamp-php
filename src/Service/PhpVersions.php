<?php
declare(strict_types=1);

namespace Studio24\MampPHP\Service;

use Studio24\MampPHP\Exception\PhpVersionException;

/**
 * Class to manage switching PHP versions
 *
 * @package MampPHP\PhpVersions
 */
class PhpVersions
{
    /**
     * Mamp application folder
     *
     * @var string
     */
    protected $mampLocation = '/Applications/MAMP';

    /**
     * Path to Mamp PHP versions, relative to $mampLocation
     *
     * @var string
     */
    protected $mampPhpLocation = 'bin/php';

    /**
     * PHP CLI symlink path, relative to $mampLocation
     *
     * @var string
     */
    protected $mampPhpCliSymlink = 'bin/php-cli';


    /**
     * Set location of MAMP application folder
     *
     * @param string $location
     */
    public function setMampLocation(string $location)
    {
        $this->mampLocation = $location;
    }

    /**
     * Return Mamp PHP folder that contains different PHP versions
     *
     * @return string
     */
    public function getMampPhpLocation()
    {
        return rtrim($this->mampLocation, '/') . '/' . rtrim($this->mampPhpLocation, '/');
    }

    public function getPhpSymlinkPath()
    {
        return rtrim($this->mampLocation, '/') . '/' . rtrim($this->mampPhpCliSymlink, '/');
    }

    /**
     *
     * ls /Applications/MAMP/bin/php/
     *
     * @return array
     */
    public function getVersions() : array
    {
        $results = [];
        $iterator = new \DirectoryIterator($this->getMampPhpLocation());
        foreach ($iterator as $fileinfo) {
            if (preg_match('/^php([0-9\.]+)$/', $fileinfo->getFilename(), $m)) {
                $results[] = $m[1];
            }
        }
        return $results;
    }

    public function getCurrentVersion()
    {
        $cmd = new CommandRunner();
        $results = $cmd->exec('which php');

        // Make sure results are up-to-date since symlink can change
        clearstatcache(true);
        $results = realpath($results);
        if ($results === false) {
            throw new PhpVersionException('Cannot detect current PHP version');
        }

        if (!preg_match('!^' .  $this->getMampPhpLocation() . '/php([0-9\.]+)!', $results, $m)) {
            return 'Not using a version of PHP from MAMP (using: ' . $results . ')';
        }

        return $m[1];
    }

    /**
     * Switch PHP version
     *
     * @param $version
     *
     * @throws PhpVersionException
     * @throws \Studio24\MampPHP\Exception\ExecException
     */
    public function switchPhp($version)
    {
        $versions = $this->getVersions();
        if (!in_array($version, $versions)) {
            throw new PhpVersionException("Cannot switch to version $version since this does not exist!");
        }

        $php = $this->getMampPhpLocation() . '/php' . $version;
        $link = $this->getPhpSymlinkPath();

        $cmd = new CommandRunner();
        $cmd->exec("ln -sfn $php $link");
    }

    /**
     * Setup bash profile and reload it if changed
     *
     * @throws PhpVersionException
     * @throws \Studio24\MampPHP\Exception\ExecException
     */
    public function setupBashProfile()
    {
        $cmd = new CommandRunner();
        $bashProfile = $cmd->exec('ls ~/.bash_profile');

        $contents = file_get_contents($bashProfile);

        // Build inserted content
        $comment = '# START MAMP PHP version #';
        $phpPath = $this->getPhpSymlinkPath() . '/bin';
        $mysqlPath = $this->mampLocation . '/Library/bin/mysql';
        $endComment = '# END MAMP PHP version #';
        $insertContent = <<<EOD
$comment
export PATH=$phpPath:$mysqlPath:\$PATH
$endComment

EOD;

        $regex = preg_quote($comment, '!');

        // Already there?
        if (preg_match('!' . $regex . '!', $contents)) {
            return;
        }

        if (!file_put_contents($bashProfile, $contents . PHP_EOL . $insertContent)) {
            throw new PhpVersionException("Cannot set paths in bash_profile");
        }

        // Reload
        $cmd->exec("source $bashProfile");
    }

}