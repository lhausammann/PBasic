<?
class Scope {
	
	private $scope = array(
		"true" => true,
		"false" => false
	
	);
	
	public function current() {
		return $this;
	}
	
	public function getVar($name) {
		if ($this->has($name)) {
			$scope = $this->current()->scope;
			return $scope[$name];
		} 
		throw new Exception("Var " . $name . ' not found in scope.');
	}
	
	public function setVar($name, $value) {
		$scope = &$this->current()->scope;
		$scope[$name] = $value;
	}
	
	public function has($name) {
		$scope = $this->current()->scope;
		return isset($scope[$name]);
	}
	
	public function resolve($name) {
		return $this->current()->getVar($name);
	}
}

class NestedScope extends Scope {
	private $scopes = array();
	
	public function __construct() {
		$this->scopes[] = new Scope();
	}
	
	public function current() {
		return $this->scopes[count($this->scopes) - 1];
	}
	
	public function pop() {
		array_pop($this->scopes);
	}
	
	public function push() {
		$this->scopes[] = new Scope();
	}
}

class GlobalScope extends NestedScope {
	protected $globals = null;
	public function __construct() {
		$this->globals = new Scope();
		parent::__construct();
	}
	
	public function setGlobalVar($name, $value) {
		$this->globals[$name] = $value;
	}
	
	public function getGlobalVar($name, $value) {
		return $this->globals->get($name);
	}
	
	public function has($name) {
		return parent::has($name) || $this->globals->has($name);
	}
	
	public function resolve($name) {
		return 
			($this->globals->has($name) ?
			$this->globals->resolve($name) :
			parent::resolve($name));
	}
}