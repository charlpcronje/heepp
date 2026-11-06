<?php
use core\extension\database\Model;

class Company extends Controller {
    function getCompanies($status = 'active') {
        return (new Model('company'))->loadDataSet($status)->runDataSet()->results;
    }

    function byId($companyId) {
        Model::
        return (new Model('company'))->loadDataSet('byId')->id($companyId)->runDataSet()->results[0];
    }

    function activeCompanies() {
        return (new Model('company'))->loadDataSet('active')->runDataSet()->results;
    }

    function viewCompany($companyId = null) {
        if (isset($companyId)) {
            $this->setData('company', $this->byId($companyId));
            $heading = 'Update Company';
        } else {
            $this->setData('company',['status_id' => 1]);
            $heading = 'Add Company';
        }
        $this->setData('companyType', (new CompanyType()) -> activeCompanyTypes());
        $this->setData('industry',    (new Industry())    -> activeIndustries());
        $this->setData('paymentTerm', (new PaymentTerm()) -> activePaymentTerms());
        $this->setData('status',      (new \Base\Status())-> allStatuses());
        $this->setOffcanvas($heading,(new coreFO('views/fgx/company/editCompany.pml',$this))->html,'500px');
    }

    function viewCompanies($status = 'active') {
        $this->setData($status,'active');
        $this->setData('company',$this->getCompanies($status));
        new \fgxCore\element\madmin\ui\breadcrumbs([
            'Companies'      => '',
            'View Companies' => 'Company/viewCompanies'
        ]);
        $this->setHtml('#wcontent',(new coreFO('views/fgx/company/viewCompanies.pml',$this))->html);
    }

    function updateCompany($companyId) {
        (new Model('company'))->update([
            'id'                => $companyId,
            'payment_terms_id'  => $this->input('payment_terms_id'),
            'industry_id'       => $this->input('industry_id'),
            'company_type_id'   => $this->input('company_type_id'),
            'company_name'      => $this->input('company_name'),
            'vat_number'        => $this->input('vat_number'),
            'user_id'           => $this->session('user')['id'],
            'status_id'         => $this->input('status_id')
        ]);
        $this->setNotify('success','Company Saved');
        $this->viewCompanies();
        $this->setClick(".offcanvasCloseButton");
    }

    function saveCompany($companyId = null) {
        if (!empty($companyId)) {
            $this->updateCompany($companyId);
        } else {
            $this->addCompany();
        }
    }

    function deleteCompany($companyId = null) {
        if (!empty($companyId)) {
            (new Model('company'))->update([
                'id'        => $companyId,
                'status_id' => 4
            ]);

            $this->setNotify('info','Company Marked as Deleted');
            $this->viewCompanies();
        }
    }

    function locations($companyId,$locationId = null) {
        if (!empty($locationId)) {
            $location = (new Model('location'))->loadDataSet('byId')
                            ->id($locationId)
                            ->runDataSet()
                            ->results[0];
            $this->setData('location',$location);
            $this->setClick(".offcanvasCloseButton");
        }
        $this->setData('locations',(new Location()) -> byCompanyId($companyId));
        $this->setData('country',  (new Country())  -> activeCountries());
        $this->setData('region',   (new Region())   -> activeRegions());
        $this->setData('status',   (new Status())   -> allStatuses());
        $this->setData('company_id',$companyId);
        $this->setData('locationsCount',count($this->getData('locations')));
        $this->setOffcanvas('Company Locations',(new coreFO('views/fgx/location/viewLocations.pml',$this))->html,'800px');
    }

    function deleteLocation($locationId = null) {
        $companyId = (new Model('location'))->loadDataSet('byId')
                         ->id($locationId)
                         ->runDataSet()
                         ->results[0]['foreign_id'];

        if (!empty($locationId)) {
            (new Model('location'))->update([
                'id'        => $locationId,
                'status_id' => 4
            ]);

            $this->setNotify('info','Location Marked as Deleted');
            $this->setClick(".offcanvasCloseButton");
            $this->locations($companyId);
        }
    }

    function updateLocation($locationId) {
        (new Model('location'))->update([
            'id'             => $locationId,
            'country_id'     => $this->input('country_id'),
            'region_id'      => $this->input('region_id'),
            'city_name'      => $this->input('city_name'),
            'suburb_name'    => $this->input('suburb_name'),
            'postal_code'    => $this->input('postal_code'),
            'street_address' => $this->input('street_address'),
            'user_id'        => $this->session('user')['id']
        ]);
        $this->setNotify('success','Location Saved');
        $this->setClick(".offcanvasCloseButton");
        $this->locations($this->input('foreign_id'));
    }

    function addLocation() {
        (new Model('location'))->insert([
            'foreign_table'  => 'companies',
            'foreign_id'     => $this->input('foreign_id'),
            'country_id'     => $this->input('country_id'),
            'region_id'      => $this->input('region_id'),
            'city_name'      => $this->input('city_name'),
            'suburb_name'    => $this->input('suburb_name'),
            'postal_code'    => $this->input('postal_code'),
            'street_address' => $this->input('street_address'),
            'user_id'        => $this->session('user')['id']
        ]);
        $this->setNotify('success','Location Added');
        $this->setClick(".offcanvasCloseButton");
        $this->locations($_POST['foreign_id']);
    }

    function saveLocation($locationId = null) {
        if (!empty($locationId)) {
            $this->updateLocation($locationId);
        } else {
            $this->addLocation();
        }
    }

    // ========================= ACCOUNTS

    function accounts($companyId,$accountId = null) {
        if (!empty($accountId)) {
            $account = (new Model('account'))->loadDataSet('byId')
                           ->id($accountId)
                           ->runDataSet()
                           ->results[0];
            $this->setData('account',$account);
            $this->setClick(".offcanvasCloseButton");
        }
        $this->setData('accounts',   (new Account())     -> byCompanyId($companyId));
        $this->setData('bank',       (new Bank())        -> activeBanks());
        $this->setData('accountType',(new AccountType()) -> activeAccountTypes());
        $this->setData('status',     (new Status())      -> allStatuses());
        $this->setData('company_id',$companyId);
        $this->setData('accountsCount',count($this->getData('accounts')));
        $this->setOffcanvas('Company Accounts',(new coreFO('views/fgx/account/viewAccounts.pml',$this))->html,'800px');
    }

    function deleteAccount($accountId = null) {
        $companyId = (new Model('account'))->loadDataSet('byId')
                         ->id($accountId)
                         ->runDataSet()
                         ->results[0]['foreign_id'];
        if (!empty($accountId)) {
            (new Model('account'))->update([
                'id'        => $accountId,
                'status_id' => 4
            ]);

            $this->setNotify('info','Account Marked as Deleted');
            $this->setClick(".offcanvasCloseButton");
            $this->accounts($companyId);
        }
    }

    function updateAccount($accountId) {
        (new Model('account'))->update([
            'id'              => $accountId,
            'account_type_id' => $this->input('account_type_id'),
            'bank_id'         => $this->input('bank_id'),
            'branch_code'     => $this->input('branch_code'),
            'branch_name'     => $this->input('branch_name'),
            'account_number'  => $this->input('account_number'),
            'account_name'    => $this->input('account_name'),
            'user_id'         => $this->session('user')['id']
        ]);
        $this->setNotify('success','Account Saved');
        $this->setClick(".offcanvasCloseButton");
        $this->accounts($this->input('foreign_id'));
    }

    function addAccount() {
        (new Model('account'))->insert([
            'foreign_table'   => 'companies',
            'foreign_id'      => $this->input('foreign_id'),
            'account_type_id' => $this->input('account_type_id'),
            'bank_id'         => $this->input('bank_id'),
            'branch_code'     => $this->input('branch_code'),
            'branch_name'     => $this->input('branch_name'),
            'account_number'  => $this->input('account_number'),
            'account_name'    => $this->input('account_name'),
            'user_id'         => $this->session('user')['id']
        ]);
        $this->setNotify('success','Account Added');
        $this->setClick(".offcanvasCloseButton");
        $this->accounts($this->input('foreign_id'));
    }

    function saveAccount($accountId = null) {
        if (!empty($accountId)) {
            $this->updateAccount($accountId);
        } else {
            $this->addAccount();
        }
    }

    // ========================= CONTACTS

    function contacts($companyId,$contactId = null) {
        if (!empty($contactId)) {
            $contact = (new Model('contact'))->loadDataSet('byId')
                           ->id($contactId)
                           ->runDataSet()
                           ->results[0];
            $this->setData('contact',$contact);
            $this->setClick(".offcanvasCloseButton");
        }
        $this->setData('contacts',(new Contact()) -> byCompanyId($companyId));
        $this->setData('status',  (new Status())  -> allStatuses());
        $this->setData('company_id',$companyId);
        $this->setData('contactsCount',count($this->getData('contacts')));
        $this->setOffcanvas('Company Contacts',(new coreFO('views/fgx/contact/viewContacts.pml',$this))->html,'800px');
    }

    function deleteContact($contactId = null) {
        $companyId = (new Model('contact'))->loadDataSet('byId')
                         ->id($contactId)
                         ->runDataSet()
                         ->results[0]['foreign_id'];
        if (!empty($contactId)) {
            (new Model('contact'))->update([
                'id'        => $contactId,
                'status_id' => 4
            ]);

            $this->setNotify('info','Contact Marked as Deleted');
            $this->setClick(".offcanvasCloseButton");
            $this->contacts($companyId);
        }
    }

    function updateContact($contactId) {
        (new Model('contact'))->update([
            'id'                 => $contactId,
            'contact_first_name' => $this->input('contact_first_name'),
            'contact_last_name'  => $this->input('contact_last_name'),
            'contact_email'      => $this->input('contact_email'),
            'contact_tel'        => $this->input('contact_tel'),
            'contact_mobile'     => $this->input('contact_mobile'),
            'contact_fax'        => $this->input('contact_fax'),
            'user_id'            => $this->session('user')['id']
        ]);
        $this->setNotify('success','Account Saved');
        $this->setClick(".offcanvasCloseButton");
        $this->contacts($this->input('foreign_id'));
    }

    function addContact() {
        (new Model('contact'))->insert([
            'foreign_table'      => 'companies',
            'foreign_id'         => $this->input('foreign_id'),
            'contact_first_name' => $this->input('contact_first_name'),
            'contact_last_name'  => $this->input('contact_last_name'),
            'contact_email'      => $this->input('contact_email'),
            'contact_tel'        => $this->input('contact_tel'),
            'contact_mobile'     => $this->input('contact_mobile'),
            'contact_fax'        => $this->input('contact_fax'),
            'user_id'            => $this->session('user')['id']
        ]);
        $this->setNotify('success','Contact Added');
        $this->setClick(".offcanvasCloseButton");
        $this->contacts($_POST['foreign_id']);
    }

    function saveContact($contactId = null) {
        if (!empty($contactId)) {
            $this->updateContact($contactId);
        } else {
            $this->addContact();
        }
    }
}
