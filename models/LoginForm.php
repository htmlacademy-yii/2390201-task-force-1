<?php
namespace app\models;
use yii\base\Model;

class LoginForm extends Model
{
  public $email;
  public $password;

  private $_user;

  public function rules()
  {
    return [
      [['email', 'password'], 'required', 'message' => 'Поле не может быть пустым'],
      ['password', 'validatePassword'],
    ];
  }

  /**
   * Validates the password.
   * This method serves as the inline validation for password.
   *
   * @param string $attribute the attribute currently being validated
   * @param array $params the additional name-value pairs given in the rule
   */
  public function validatePassword($attribute, $params)
  {
    if (!$this->hasErrors()) {
      $user = $this->getUser();
      if (!$user || !$user->validatePassword($this->password)) {
        $this->addError($attribute, 'Неправильный email или пароль');
      }
    }
  }

  /**
   * Finds user by [[email]]
   *
   * @return User|null
   */
  public function getUser()
  {
    if ($this->_user === null) {
      $this->_user = User::findOne(['email' => $this->email]);
    }

    return $this->_user;
  }
}
