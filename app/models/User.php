<?php 
namespace App\Models;

/*
* ModelName: User
* Author:	JZCoding
* Author URI:	http://jzcoding.com
*/

use Core\Model;

/**
* @Entity
* @Table(name="jz_users")
*/

class User extends Model {
	/**
	* @Id
	* @GeneratedValue
	* @Column(type="integer", length=11, nullable=false, unique=true)
	*/
	protected $id;
	/**
	* @Column(type="string", length=200, nullable=false, unique=true)
	*/
	protected $username;
	/**
	* @Column(type="string", length=255, nullable=false)
	*/
	protected $password;
	/**
	* @Column(type="string", length=255, nullable=false)
	*/
	protected $first_name;
	/**
	* @Column(type="string", length=255, nullable=false)
	*/
	protected $last_name;
	/**
	* @Column(type="string", length=255, nullable=false, unique=true)
	*/
	protected $email;


    public function __construct() {
    }

	/**
	 * @return mixed
	 */
	public function getId()
	{
	    return $this->id;
	}

	/**
	 * @param mixed $id
	 *
	 * @return self
	 */
	public function setId($id)
	{
	    $this->id = $id;

	    return $this;
	}
	/**
	 * @return mixed
	 */
	public function getUsername()
	{
	    return $this->username;
	}

	/**
	 * @param mixed $username
	 *
	 * @return self
	 */
	public function setUsername($username)
	{
	    $this->username = $username;

	    return $this;
	}
	/**
	 * @return mixed
	 */
	public function getPassword()
	{
	    return $this->password;
	}

	/**
	 * @param mixed $password
	 *
	 * @return self
	 */
	public function setPassword($password)
	{
	    $this->password = $password;

	    return $this;
	}


    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     *
     * @return self
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     *
     * @return self
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}