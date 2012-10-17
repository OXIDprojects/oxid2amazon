<?php
class az_amz_ftp extends oxSuperCfg
{
	/**
	 * Amazon Config object
	 * @var az_amz_config
	 */
	protected $_oAZConfig = null;
	
	/**
	 * Extension to be used for ftp (ftp or curl)
	 * @var string
	 */
	protected $_extension = 'ftp';
	
	/**
	 * Ftp hostname
	 * @var string
	 */
	protected $_sHost = null;
	
	/**
	 * Ftp username
	 * @var string
	 */
	protected $_sUser = null;
	
	/**
	 * Ftp password
	 * @var string
	 */
	protected $_sPassword = null;
	
	/**
	 * Passive mode on/off
	 * @var bool
	 */
	protected $_blPassive = false;
	
	
	public function init()
	{
		$ret = parent::init();
		
		$this->_oAZConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
		
		return $ret;
	}		
	
		
	/**
	 * Connects to ftp server
	 * @param string $sHost Server hostname
	 * @param string $sUserName Username
	 * @param string $sPassword passwords
	 * @param bool $blPassive Passive mode
	 * 
	 * @return bool
	 */
	public function connect($sHost, $sUsername, $sPassword, $blPassive = false)
	{
		$this->_setParams($sHost, $sUsername, $sPassword, $blPassive);
		
		switch($this->_extension)
		{
			case 'curl':
				//todo: implement via curl
			break;
			default:
				$oFtp 	= ftp_connect($sHost);
				$res 	= ftp_login($oFtp, $sUsername, $sPassword);
				// passive mode on/off
				ftp_pasv($oFtp, $blPassive);
			break;
		}
		
		if ($res)
		{
			$this->_oFtpHandler = $oFtp;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Upload file to specified path on ftp server
	 * 
	 * @param string $sLocalFile path to local file
	 * @param string $sRemoteFile path on server where file must be uploaded
	 * @param string $sRemoteDir remote dir
	 * @return bool
	 */
	public function uploadFile($sLocalFile, $sRemoteFile, $sRemoteDir = null)
	{
		if (!$this->_oFtpHandler)
			return false;
		
		$blRet = false;
		 		
		switch($this->_extenstion)			
		{
			case 'curl':
				//todo: curl implementation
			break;
			default:
				$blRet = $this->_ftpUploadFile($sLocalFile, $sRemoteFile, $sRemoteDir);
			break;
		}
						
		return $blRet;
	}
	
	/**
	 * Upload file via ftp extension
	 * 
	 * @param string $sLocalFile path to local file
	 * @param string $sRemoteFile remote file name
	 * @param string $sRemotePath remote dir
	 * 
	 * @return bool
	 */
	protected function _ftpUploadFile($sLocalFile, $sRemoteFile, $sRemoteDir = null)
	{				
		$blSuccess = true;
		
		if (ftp_chdir($this->_oFtpHandler, $sRemoteDir))
		{
			$sLocalFileTmp = $sLocalFile;
			$iFileIndex = 0;
			// main file
			
			$sLocalFile .= '.xml';
			
			while(file_exists($sLocalFile))
			{
				if (!ftp_put($this->_oFtpHandler, $sRemoteFile, $sLocalFile, FTP_ASCII))
					$blSuccess = false;
				
				if (!$blSuccess)
					break;
					
				$iFileIndex++;
							
				$sLocalFile = $sLocalFileTmp . "_" . $iFileIndex. ".xml";
			}
		}
		
		return $blSuccess;
	}
	
	
	public function ftp_sync ($sLocalDir, $sRemoteDir, $sType) 
	{
		$iFiles = 0;
		if ($sLocalDir != ".") {
	        if (ftp_chdir($this->_oFtpHandler, $sRemoteDir) == false) {
	            echo ("Change Dir Failed: $sRemoteDir<BR>\r\n");
	            return false;
	        }
	        if (!(is_dir($sLocalDir)))
	            mkdir($sLocalDir);
	        chdir ($sLocalDir);
	    }
	
	    $contents = ftp_nlist($this->_oFtpHandler, ".");
	    foreach ($contents as $file) {
	    	
	   
	        if ($file == '.' || $file == '..' || substr($file, 0, strlen($sType)) != $sType) {
	            continue;
	        }
	       
	        if (@ftp_chdir($this->_oFtpHandler, $file)) {
	            ftp_chdir ($this->_oFtpHandler, "..");
	            ftp_sync ($file);
	        }
	        else {
	            ftp_get($this->_oFtpHandler, $file, $file, FTP_BINARY);
	            $iFiles++;
	        }
	    }
	    return $iFiles;
	} 
	
	
	
	/**
	 * Sets connection parameters
	 * @param string $sHost Ftp server
	 * @param string $sUser Username
	 * @param string $sPassword Password
	 * @param bool $blPassive Passive mode on/off
	 * 
	 */
	protected function _setParams($sHost, $sUser, $sPassword, $blPassive)
	{
		$this->_sHost 		= $sHost;
		$this->_sUser 		= $sUser;
		$this->_sPassword	= $sPassword;
		$this->_blPassive	= $blPassive;
	}
	
	public function deleteFile($sRemoteFile, $sRemoteDir)
	{
		ftp_delete($this->_oFtpHandler, $sRemoteDir."/".$sRemoteFile);
	}
	
	
}
?>
