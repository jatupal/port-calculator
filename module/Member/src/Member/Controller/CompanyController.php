<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Member for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Member\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Member\Model\Company;
use Member\Form\CompanyForm;
use Zend\View\Model\ViewModel;
class CompanyController extends AbstractActionController
{
	protected $companyTable;

	 public function getCompanyTable()
     {
         if (!$this->companyTable) {
             $sm = $this->getServiceLocator();
             $this->companyTable = $sm->get('Member\Model\CompanyTable');
         }
         return $this->companyTable;
     }
    public function indexAction()
    {
       return new ViewModel(array(
             'companies' => $this->getCompanyTable()->fetchAll(),
         ));
    }

    public function addAction()
    {
		 $form = new CompanyForm();
         $form->get('submit')->setValue('Add');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $company = new Company();
             $form->setInputFilter($company->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $company->exchangeArray($form->getData());
                 $this->getCompanyTable()->saveCompany($company);
                 // Redirect to list of albums
				 return $this->redirect()->toRoute('company', array(
                 'action' => 'index'));
             }
         }
         return array('form' => $form);
    }
	    public function editAction()
    {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('company', array(
                 'action' => 'add'
             ));
         }
         try {
             $company = $this->getCompanyTable()->getCompany($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('company', array(
                 'action' => 'index'
             ));
         }
         $form  = new CompanyForm();
         $form->bind($company);
         $form->get('submit')->setAttribute('value', 'Edit');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($company->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $this->getCompanyTable()->saveCompany($company);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('company', array(
                 'action' => 'index'
             ));
             }
         }
         return array(
             'id' => $id,
             'form' => $form,
         );
    }
}
