<?php namespace App\Controllers;

use App\Models\UsersModel;

class Users extends BaseController
{
	public function index()
	{
		$data = [];
		helper(['form']);

		if($this->request->getMethod() == 'post'){
			//validation rules here
			$rules = [				
				'email' => 'required|min_length[7]|max_length[20]|valid_email',
				'password' => 'required|min_length[6]|max_length[12]|validateUser[email,password]'
			];

			$errors = [
				'password' => [
					'validateUser' => 'Email or Password don\'t match'
				]
			];

			if(! $this->validate($rules, $errors)) {
				$data['validation'] = $this->validator;
			}else{
				$model = new UsersModel();

				$user = $model->where('email', $this->request->getVar('email'))
							->first();

				$this->setUserSession($user);
				return redirect()->to('/dashboard');


				
			}
		}

		echo view('templates/header', $data);
		echo view('login');
		echo view('templates/footer');
	}

	private function setUserSession($user){
		$data = [
			'id' => $user['id'],
			'firstname' => $user['firstname'],
			'lastname' => $user['lastname'],
			'email' => $user['email'],
			'isLoggedIn' => true
		];

		session()->set($data);
		return true;
	}

	public function register(){
		$data = [];
		helper(['form']);

		if($this->request->getMethod() == 'post'){
			//validation rules here
			$rules =[
				'firstname' => 'required|min_length[2]|max_length[20]',
				'lastname' => 'required|min_length[2]|max_length[20]',
				'email' => 'required|min_length[7]|max_length[20]|valid_email|is_unique[users.email]',
				'password' => 'required|min_length[6]|max_length[12]',
				'conf_password' => 'matches[password]'
			];

			if(! $this->validate($rules)) {
				$data['validation'] = $this->validator;
			}else{
				$model = new UsersModel();

				$newData = [
					'firstname' => $this->request->getVar('firstname'),
					'lastname' => $this->request->getVar('lastname'),
					'email' => $this->request->getVar('email'),
					'password' => $this->request->getVar('password'),
				];

				$model->save($newData);
				$session = session();
				$session->setFlashdata('success', 'Successfully Registered!');
				return redirect()->to('/test');
			}
		}

		echo view('templates/header', $data);
		echo view('registration');
		echo view('templates/footer');
	}

}
