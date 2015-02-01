<?php

interface RequestManager {
	
	//Variable number of parameters, so use
	//foreach (func_get_args() as $a)
	//to iterate over parameters 
	public function getRequest();
	
}
