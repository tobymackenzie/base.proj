<?php
//-# only here because I'm not sure how to do this with YAML
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return function(ContainerConfigurator $conf){
	$params = $conf->parameters();
	$params->set('baseProjOpts', []);
	$params->set('paths.project', TJM_PROJECT_DIR);
	foreach([
		__DIR__ . '/console.local.php',
		__DIR__ . '/console.local.yml',
	] as $file){
		if(file_exists($file)){
			$conf->import($file);
		}
	}
};
