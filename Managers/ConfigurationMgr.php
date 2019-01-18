<?php
require_once($ConstantsArray['dbServerUrl']. "BusinessObjects/Configuration.php");
require_once($ConstantsArray['dbServerUrl']. "DataStores/BeanDataStore.php");
class ConfigurationMgr{
		private static $configurationMgr;
		private static $configurationDataStore;
		
		
		public static function getInstance(){
			if (!self::$configurationMgr){
				self::$configurationMgr = new ConfigurationMgr();
				self::$configurationDataStore = new BeanDataStore(Configuration::$className,Configuration::$tableName);
			}
			return self::$configurationMgr;
		}
		
		public function getConfiguration($configKey,$defaultValue=null){
			$colValuePair['configkey'] = $configKey;
			$configuration = self::$configurationDataStore->executeConditionQuery($colValuePair);
			if($configuration == null){
				return $defaultValue;
			}else{
				return $configuration[0]->getConfigValue();
			}	
		}
		
		public function getConfigurationObject($configKey){
			$colValuePair['configkey'] = $configKey;
			$configuration = self::$configurationDataStore->executeConditionQuery($colValuePair);
			if(!empty($configuration)){
				return $configuration[0];
			}
			return null;
		}
		
		public function saveConfiguration($configKey,$configValue){
			$existingConfiguration = $this->getConfigurationObject($configKey);
			$id = 0;
			if(!empty($existingConfiguration)){
				$id = $existingConfiguration->getSeq();
			}
			$config = new Configuration();
			$config->setSeq($id);
			$config->setConfigKey($configKey);
			$config->setConfigValue($configValue);
			$id = self::$configurationDataStore->save($config);
			return $id;
		}
}