<?php

namespace HCStudio;

class Session {
	private static $instance;
	private static $isStarted;
	private static $session;
	private $segment;
	private $data;

	public static function getInstance() {
    if(!self::$instance instanceof self) {
       self::$instance = new self;
    }
    return self::$instance;
  }

	public function __construct($segment = null) {
		$this->setSegMent($segment);
	}
	public function setSegMent($segment = null) {
		if(!headers_sent())
		{
			# Si objeto fue iniciado previamente
			if (is_null(self::$isStarted)) {
				session_start();
				self::$session =& $_SESSION;
				self::$isStarted = true;
			}
			# Definimos nombre de segmento
			if (!is_null($segment) && is_string($segment)) {
				$this->segment = '_'.$segment.'_';
			} else {
				$this->segment = '_default_';
			}
			# Obtenemos referencia de session
			$session =& $this->getSession();
			# Si no existe segmento en $_SESSION lo creamos
			if (!isset($session[$this->segment])) {
				$session[$this->segment] = array();
			}
			# Asigmanos referencia de segmento en atributo data
			$this->data =& $session[$this->segment];
		}
	}

	# Obtenemos referencia a $_SESSION
	private function &getSession() {
		return self::$session;
	}
	# Obtenemos id de la session actual
	public function getId() {
		return session_id();
	}
	# Nombre de la session actual
	public function getName() {
		return session_name();
	}
	# Liberamos llaves de data
	public function clear($keys) {
		if (is_string($keys) && !empty($keys)) {
			# Dividimos cadena por punto
			$aKeys = explode('.', $keys);
			# Contamos elementos en llaves (menos uno, que es desde donde apuntamos al valor)
			$kCount = count($aKeys)-1;
			# Si solo es una cadena
			if ($kCount == 0) {
				# Regresa la cadena si existe, de lo contrario false
				if (isset($this->data[$aKeys[0]] )) {
					unset($this->data[$aKeys[0]]);
					return true;
				}
				return false;
			}
			# Valor para iniciar a buscar
			$currentValue =& $this->data;
			# Ciclo para navegar llaves
			for ($a = 0; $a < $kCount; $a++) {
				# Si no existe key en current array, retorna false
				if (!isset($currentValue[$aKeys[$a]])) return false;
				# Guardamos current key
				$currentValue =& $currentValue[$aKeys[$a]];
				# En penultima llave
				if ($a==$kCount-1) {
					# Adelantamos para limpiar objetivo
					unset($currentValue[$aKeys[$a+1]]);
					return true;
				}
			}
		}
		return false;
	}
	# Seteamos valores en data
	public function set($keys, $val='') {
		# Si es texto
		if (is_string($keys) && !empty($keys)) {
			# Dividimos cadena por puntos
			$aKeys = explode('.', $keys);
			# Contamos elementos en llaves (menos uno, que es donde metemos el valor)
			$kCount = count($aKeys)-1;
			# Si solo es una cadena
			if ($kCount == 0) {
				# Creamos nuevo valor en data
				$this->data[$keys] = $val;
				return $this;
			}
			# Guardamos llaves en data
			$aTmp =& $this->data;
			# Asignamos llaves
			for ($i = 0; $i <= $kCount; $i++) {
				# Si es la ultima ronda
				if ($i == $kCount) {
					$aTmp[$aKeys[$i]] = $val;
				} else {
					if (!isset($aTmp[$aKeys[$i]]) || !is_array($aTmp[$aKeys[$i]])) {
						$aTmp[$aKeys[$i]] = array();
					}
				}
				# Asignamos puntero
				$aTmp =& $aTmp[$aKeys[$i]];
			}
			return $this;
		}
		return false;
	}
	# Leemos variable en data
	public function getData() {
		return $this->data;
	}
	public function get($keys='') {
		# Si es texto
		if (is_string($keys)) {
			# Dividimos cadena por punto
			$aKeys = explode('.', $keys);
			# Contamos elementos en llaves
			$kCount = count($aKeys);
			# Si cadena esta vacia
			if (empty($keys)) return $this->data;
			# Si solo es una cadena
			if ($kCount == 1) {
				# Regresa la cadena si existe, de lo contrario false
				return (isset($this->data[$aKeys[0]])) ? $this->data[$aKeys[0]] : false;
			}
			# Valor para iniciar a buscar
			$currentValue = $this->data;
			# Ciclo para navegar llaves
			for ($i = 0; $i < $kCount; $i++) {
				# Si no existe key en current array, retorna false
				if (!isset($currentValue[$aKeys[$i]])) return false;
				# Guardamos current key
				$currentValue = $currentValue[$aKeys[$i]];
			}
			# Retorna valor current
			return $currentValue;
		}
		return false;
	}
	# Destruimos data
	public function destroy() {
		$session =& $this->getSession();
		unset($session[$this->segment]);
	}
	# Asignamos nuevo valor flash en data
	public function setFlash($key, $val) {
		$this->data['__flash'][$key] = $val;
	}
	# Obtenemos valor flash y despues eliminamos llave en data
	public function getFlash($key) {
		if (isset($this->data['__flash'][$key])) {
			$val = $this->data['__flash'][$key];
			unset($this->data['__flash'][$key]);
			return $val;
		}
		return false;
	}
	# Verifica la existencia de valor flash en data
	public function hasFlash($key) {
		return isset($this->data['__flash'][$key]);
	}
	# Elimina todos los valores flash en data
	public function clearFlash() {
		unset($this->data['__flash']);
	}
	# Regeneramos id de session
	public function regenerateId() {
		session_regenerate_id();
	}
	# Resetea session
	public function resetSession($recover = false) {
		# Guardamos copia de session
		$recovered = $this->getSession();
		# Destruimos session
		session_unset();
		session_destroy();
		# Regeneramos session
		session_start();
		self::$session =& $_SESSION;
		# Restauramos copia de session
		if ($recover) self::$session = $recovered;
	}
}

?>