<?php 
namespace Core;

class Response {
	private $_status;
	private $_message;
	private $_data;
	public function __construct($status = false, $data = null, $message = '') {
		$this->_status = $status;
		$this->_data = $data;
		$this->_message = $message;
	}

	

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @param mixed $_status
     *
     * @return self
     */
    public function setStatus($_status)
    {
        $this->_status = $_status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param mixed $_message
     *
     * @return self
     */
    public function setMessage($_message)
    {
        $this->_message = $_message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param mixed $_data
     *
     * @return self
     */
    public function setData($_data)
    {
        $this->_data = $_data;

        return $this;
    }
}