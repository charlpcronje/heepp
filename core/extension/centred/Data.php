<?php
namespace core\extension\centred;
use core\extension\Extension;
use core\extension\database\Model;
use core\extension\database\Database;
use core\system\api;

class Data extends Extension {
    public function __construct() {
        parent::__construct();
    }

    public static function get($method,$params = null) {
        $data = new Data();

        if (is_array($params)) {
            return call_user_func_array([$data,$method],$params);
        }
        if (isset($params)) {
            return $data->$method($params);
        }
        return $data->$method();
    }

    public function getShopContactTypes() {
        return Model::mold('/sc/shopContactType')->all()->get();
    }

    public function getShopBrands() {
        $allBrands = (new Model('/sc/shopBrands'))->loadDataSet('all')->runDataSet()->results;
        $brands = [];
        foreach($allBrands as $brand) {
            $brands[$brand->id] = $brand;
        }
        return $brands;
    }

    public function getShopCategories() {
        $categoriesArray = (new Model('/sc/shopCategory'))->loadDataSet('all')->runDataSet()->results;
        $categories = [];
        foreach($categoriesArray as $category) {
            $categories[$category->id] = $category;
        }

        $subCategories = (new Model('/sc/shopSubCategory'))->loadDataSet('all')->runDataSet()->results;

        foreach($subCategories as $subCategory) {
            @$categories[$subCategory->shop_category_id]->sub_categories[$subCategory->id] = $subCategory;
        }
        return $categories;
    }

    public function saveShopCategoriesAndBrands($shopId,$categories = [],$subCategories = [],array $brands = []) {
        // Delete and add shop shop categories
        $model = new Model('/sc/shopShopCategory');
        $model->delete([
            'shop_id' => $shopId
        ]);

        foreach($categories as $category) {
            $model->insert([
                'shop_id' => $shopId,
                'shop_category_id' => $category
            ]);
        }

        // Delete and add shop shop sub categories
        $model = new Model('/sc/shopShopSubCategory');
        $model->delete([
            'shop_id' => $shopId
        ]);

        //foreach($subCategories as $subCategory) {
        //    $model->insert([
        //        'shop_id' => $shopId,
        //        'shop_subcategory_id' => $subCategory
        //    ]);
        //}

        // Delete and add shop brands
        $model = new Model('/sc/shopBrand');
        $model->delete([
            'shop_id' => $shopId
        ]);

        foreach($brands as $brand) {
            $model->insert([
                'shop_id' => $shopId,
                'shop_brand_id' => $brand
            ]);
        }
    }

    public function addShoppingCardToShop($shopId,$shoppingCardId) {
        return (new Model('/sc/shopShoppingCard'))->insert([
            'shop_id' => $shopId,
            'shopping_card_id' => $shoppingCardId
        ]);
    }

    public function removeShoppingCardFromShop($shopId,$shoppingCardId) {
        return (new Model('/sc/shopShoppingCard'))->delete([
            'shop_id' => $shopId,
            'shopping_card_id' => $shoppingCardId
        ]);
    }

    public function getTradingHourTimes() {
        $times =  (new Model('/sc/tradingHourTime'))->loadDataSet('all')->runDataSet()->results;
        $tradingHourTimes = [];
        foreach($times as $time) {
            $tradingHourTimes[$time->id] = $time;
        }
        return $tradingHourTimes;
    }

    public function getPublicHolidays() {
        $holidays = (new Model('/sc/publicHoliday'))->loadDataSet('all')->runDataSet()->results;
        $publicHolidays = [];
        foreach($holidays as $holiday) {
            $dates = json_decode($holiday->dates);
            $holidayDates = [];
            foreach($dates as $date) {
                $holidayDates[] = $date->public_holiday_date;
            }
            $holiday->dates = implode('<br/>',$holidayDates);
            $publicHolidays[$holiday->id] = $holiday;
        }
        return $publicHolidays;
    }

    public function updateNormalTradingHours($openTimeIds,$closeTimeIds) {
        $model = new Model('/sc/shopTradingHour');
        foreach($openTimeIds as $key => $value) {
            $model->update([
                'id' => $key,
                'open_time_id' => $value,
                'close_time_id' => $closeTimeIds[$key]
            ]);
        }
    }

    public function updateHolidayTradingHours($shopId,$publicHolidayIds,$openTimeIds,$closeTimeIds) {
        $model = new Model('/sc/shopTradingHour');
        foreach($publicHolidayIds as $holidayId => $holidayDay) {
            $model->delete([
                'shop_id' => $shopId,
                'public_holiday_id' => $holidayId
            ]);

            if (!empty($openTimeIds[$holidayId]) && !empty($closeTimeIds[$holidayId])) {
                $model->insert([
                    'shop_id' => $shopId,
                    'trading_hour_type_id' => 2,
                    'day' => $holidayDay,
                    'public_holiday_id' => $holidayId,
                    'open_time_id' => $openTimeIds[$holidayId],
                    'close_time_id' => $closeTimeIds[$holidayId],
                    'created_at' => CURRENT_TIMESTAMP,
                    'updated_at' => CURRENT_TIMESTAMP
                ]);
            }
        }
    }

    public function deleteSpecialTradingHour($tradingHourId) {
        return (new Model('/sc/shopTradingHour'))->delete([
            'id' => $tradingHourId
        ]);
    }

    public function addSpecialTradingHours() {
        return (new Model('/sc/shopTradingHour'))->insert([
            'shop_id' => $this->session('shop.id'),
            'trading_hour_type_id' => 2,
            'day' => $this->input('day'),
            'special_day_date' => $this->input('special_day_date'),
            'open_time_id' => $this->input('open_time_id'),
            'close_time_id' => $this->input('close_time_id'),
            'created_at' => CURRENT_TIMESTAMP,
            'updated_at' => CURRENT_TIMESTAMP
        ]);
    }

    public function addShopContact() {
        return (new Model('/sc/shopContact'))->insert([
            'shop_id' => $this->session('shop.id'),
            'contact_title_id' => $this->input('contact_title_id'),
            'shop_contact_type_id' => $this->input('shop_contact_type_id'),
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'email' => $this->input('email'),
            'telephone' => $this->input('telephone'),
            'cell_number' => $this->input('cell_number'),
            'note' => $this->input('note'),
            'created_at' => CURRENT_TIMESTAMP,
            'updated_at' => CURRENT_TIMESTAMP
        ]);
    }

    public function deleteShopContact($contactId) {
        return (new Model('/sc/shopContact'))->delete([
            'id' => $contactId
        ]);
    }

    public function getShopProductRanges() {
        return (new Model('/sc/productRange'))->loadDataSet('byShopId')->shopId($this->session('shop.id'))->runDataSet()->results;
    }

    public function getProductRangeById($rangeId) {
        return current((new Model('/sc/productRange'))->getRecord($rangeId));
    }

    public function getProductCountByRange() {
        $db = new Database('cms');
        $db->query('SELECT product_ranges.id AS product_range_id, count(products.product_range_id) AS product_count FROM product_ranges LEFT JOIN products ON products.product_range_id = product_ranges.id WHERE shop_id = '.$this->session('shop.id').' GROUP BY product_ranges.id');
        $rangecounts = [];
        foreach($db->toObject('rangeCount') as $range) {
            $rangecounts[$range->product_range_id] =  $range;
        }
        return $rangecounts;
    }

    public function getProductCountByRangeId($rangeId) {
        $model = new Model('/sc/product');
        $model->loadDataSet('byRangeId')->rangeId($rangeId)->countDataSet();
        return $model->results[0]['count'];
    }

    public function getProductsByRangeId($rangeId) {
        return (new Model('/sc/product'))->loadDataSet('byRangeId')->rangeId($rangeId)->runDataSet()->results;
    }

    public function addProductToRange() {
        (new Model('/sc/product'))->insert([
            'product_range_id' => $this->input('product_range_id'),
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'code' => $this->input('code'),
            'price' => $this->input('price'),
            'price_special' => $this->input('price_special',$this->input('price')),
            'link' => $this->input('link'),
            'visible_to_public' => $this->input('visible_to_public',0),
            'featured' => $this->input('featured',0),
            'created_at' => CURRENT_TIMESTAMP,
            'updated_at' => CURRENT_TIMESTAMP
        ]);
    }

    public function updateProductRange($productRangeId) {
        return (new Model('/sc/productRange'))->update([
            'id' => $productRangeId,
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'visible_to_public' => $this->input('visible_to_public'),
            'updated_at' => CURRENT_TIMESTAMP
        ]);
    }

    public function addProductRange() {
        $model = new Model('/sc/productRange');
        $model->insert([
            'client_id' => $this->session('mall.client_id'),
            'shop_id' => $this->session('shop.id'),
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'visible_to_public' => $this->input('visible_to_public',0),
            'created_at' => CURRENT_TIMESTAMP,
            'updated_at' => CURRENT_TIMESTAMP
        ]);
        return $model->lastInsertId();
    }

    public function deleteProduct($productId) {
        return (new Model('/sc/product'))->delete([
            'id' => $productId
        ]);
    }

    public function getProductDetailsById($productId) {
        return (object)(new Model('/sc/product'))->getRecord($productId);
    }

    public function updateProduct($productId) {
        (new Model('/sc/product'))->update([
            'id' => $productId,
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'code' => $this->input('code'),
            'price' => $this->input('price'),
            'price_special' => $this->input('price_special',$this->input('price')),
            'link' => $this->input('link'),
            'visible_to_public' => $this->input('visible_to_public',0),
            'featured' => $this->input('featured',0),
            'updated_at' => CURRENT_TIMESTAMP
        ]);
    }

    // *********************       GALLERIES      **************************
    public function getImagesByGalleryId($galleryId) {
        return api::get('galleries/'.$galleryId)->call()[0]->gallery_files;
    }

    public function getGalleryById($galleryId) {
        return api::get('galleries/'.$galleryId)->call()[0];
    }

    public function saveGallery() {
        $model = new Model('/sc/gallery');
        return $model->update($this->input());
    }

    public function addGallery() {

        $model = new Model('/sc/gallery');
        $lastInsertId = $model->insert($this->input());

        $model = new Model('/sc/shopGallery');
        $model->insert([
            'shop_id'    => $this->input('shop_id'),
            'gallery_id' => $lastInsertId
        ]);
        return $lastInsertId;
    }

    // Marketing Zone
    // **********************        MALL         **************************
    public function getMallModelMold($mallId) {
        return Model::mold('/sc/mall')->find($mallId);
    }

    public function getMallVenues($mallId) {
        return (new Model('/sc/venue'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getMallgiftcards($mallId) {
        return (new Model('/sc/giftcard'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getMallParkades($mallId) {
        return (new Model('/sc/parkade'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getMallParkadeDetails($parkadeId) {
        return (new Model('/sc/parkadeDetails'))->loadDataSet('byParkadeId')->parkadeId($parkadeId)->runDataSet()->results;
    }

    public function getMallTradingHours($mallId) {
        return (new Model('/sc/tradingHour'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getMallClassifications() {
        return (new Model('/sc/mallClassification'))->loadDataSet('all')->runDataSet()->results;
    }

    public function saveBasicMallInfo() {
        return (new Model('/sc/mall'))->update([
            'id'                     => $this->getData('mz.mall.id'),
            'name'                   => $this->input('name'),
            'mall_classification_id' => $this->input('mall_classification_id'),
            'description'            => $this->input('description'),
            'updated_at'             => CURRENT_TIMESTAMP
        ]);
    }

    public function saveDetailedMallInfo() {
        $model = Model::mold('/sc/mall')->find($this->getData('mz.mall.id'));
        foreach($this->input() as $key => $value) {
            $model->$key = $value;
        }
        $model->updated_at = CURRENT_TIMESTAMP;
        return $model->save();
    }

    public function saveMallFeatures($mallId,$features) {
        return Model::mold('/sc/mall')->find($mallId)->set('special_features',$features)->save();
    }

    public function getAllEventsByMall($mallId) {
        return (new Model('/sc/event'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getMallLandlords($mallId) {
        return (new Model('/sc/landlord'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getAllExhibitionsByMall($mallId) {
        return (new Model('/sc/exhibition'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getAllCompetitionsByMall($mallId) {
        return (new Model('/sc/competition'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getAllJobsByMall($mallId) {
        return (new Model('/sc/job'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }


    // **********************        CLIENT       **************************
    public function getClientByAPIKey() {
        return Model::mold('/sc/client')->byAPIKey(['apiKey'=>$this->getData('app.api.cms.auth.key')])->get()->current();
    }

    public function getMallsByClientId($clientId) {
        return Model::mold('/sc/mall')->byClientId(['clientId'=>$clientId])->run();
    }

    public function getClientDetails($clientId) {
        return (object)(new Model('/sc/client'))->getRecord($clientId);
    }



    // **********************        EVENTS       **************************
    public function getEventById($eventId) {
        return (object)(new Model('/sc/event'))->getRecord($eventId);
    }


    // **********************        JOBS       **************************
    public function getJobById($jobId) {
        return (object)(new Model('/sc/job'))->getRecord($jobId);
    }

    public function getJobCategories($clientId) {
        return (new Model('/sc/jobCategory'))->loadDataSet('byClientId')->clientId($clientId)->runDataSet()->results;
    }

    public function getJobSubCategories($clientId) {
        return (new Model('/sc/jobSubCategory'))->loadDataSet('byClientId')->clientId($clientId)->runDataSet()->results;
    }

    public function deleteJobById($jobId) {
        return (new Model('/sc/job'))->delete([
            'id' => $jobId
        ]);
    }

    // **********************        MOVIES       **************************
    public function getCinemaDetails($clientId) {
        $data = Model::mold('/sc/cinema')->byClientId(['clientId'=>$clientId])->get();
        //$data = (new Model('/sc/cinema'))->loadDataSet('byClientId')->clientId($clientId)->runDataSet()->results;
        if (count($data) > 0) {
            return $data;
        } else {
            return [];
        }
    }

    public function getMovies() {
        return (new Model('/sc/movie'))->loadDataSet('current')->runDataSet()->results;
    }

    public function getMovieDetailsById($movieId) {
        return (object)(new Model('/sc/movie'))->getRecord($movieId);
    }

    public function saveCinema($cinemaId) {
        return (new Model('/sc/cinema'))->update([
            'id'        => $cinemaId,
            'name'      => $this->input('name'),
            'company'   => $this->input('company'),
            'address1'  => $this->input('address1'),
            'province'  => $this->input('province'),
            'city'      => $this->input('city'),
            'suburb'    => $this->input('suburb'),
            'telephone' => $this->input('telephone')
        ]);
    }


    // **********************        SHOPS       **************************
    public function getShopsByCategoryId($mallId,$categoryId) {
        return (new Model('/sc/shop'))->loadDataSet('byMallAndCategoryId')->mallId($mallId)->categoryId($categoryId)->runDataSet()->results;
    }

    public function getShopsByBrandId($mallId,$brandId) {
        return (new Model('/sc/shop'))->loadDataSet('byMallAndBrandId')->mallId($mallId)->brandId($brandId)->runDataSet()->results;
    }

    public function getShopsByMallId($mallId) {
        return (new Model('/sc/shop'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getShopsCanRegisterByMallId($mallId) {
        return (new Model('/sc/shop'))->loadDataSet('canRegisterByMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getShopById($shopId) {
        return Model::mold('/sc/shop')->find($shopId)->current();
    }

    // **********************        CHAIN SHOPS       **************************
    public function getAllChainShops() {
        $shops = Model::mold('/sc/chainShop')->all();
        $allShops = $shops->getAll();
        // Remove items with no logo
        foreach($allShops as $shopKey => $shopValue) {
            if (empty(trim($shopValue->logo_filename))) {
                $shops->hide($shopKey);
            }

            if (empty(trim($shopValue->logo_filename))) {
                $shops->hide($shopKey);
            }

            if (strpos($shopValue->logo_filename,'http:') === false && !empty(trim($shopValue->logo_filename))) {
                $shopValue->logo_filename = CMS_CENTRED_URL.$shopValue->logo_filename;
            }
        }
        return $shops->collection;
    }

    public function getChainShopModelMold($chainShopId) {
        return Model::mold('/sc/chainShop')->find($chainShopId);
    }

    // **********************        MEMOS       **************************
    public function getMemosByMallId($mallId) {
        return (new Model('/sc/shopCommunication'))->loadDataSet('byMallId')->mallId($mallId)->runDataSet()->results;
    }

    public function getMemosToCollection($mallId,$collection = 'all') {
        return (new Model('/sc/shopCommunication'))->loadDataSet('byMallIdAndCollection')->mallId($mallId)->collection($collection)->runDataSet()->results;
    }

    public function getMemosForCategory($mallId,$collection = 'all') {
        $model = new Model('/sc/shopCommunication');
        $model->loadDataSet('byMallIdForCategory')->mallId($mallId)->collection($collection)->runDataSet();
        $results = $model->results;
        if (!empty($results[0]->id)) {
            return $model->results;
        } else {
            return [];
        }
    }

    public function getMemosForBrand($mallId,$collection = 'all') {
        $model = new Model('/sc/shopCommunication');
        $model->loadDataSet('byMallIdForBrand')->mallId($mallId)->collection($collection)->runDataSet();
        $results = $model->results;
        if (!empty($results[0]->id)) {
            return $model->results;
        } else {
            return [];
        }
    }

    public function addMemoToShops($shopIds,$collection,$collectionDesc,$collectionId = null) {
        $lastInsertId = (new Model('/sc/shopCommunication'))->insert([
            'mall_id'                => $this->getData('mz.mall.id'),
            'collection'             => $collection,
            'collection_description' => $collectionDesc,
            'collection_id'          => $collectionId,
            'title'                  => $this->input('title'),
            'message'                => $this->input('message'),
            'display_from'           => $this->input('display_from'),
            'display_to'             => $this->input('display_to'),
            'created_at'             => CURRENT_TIMESTAMP,
            'updated_at'             => CURRENT_TIMESTAMP
        ]);

        $model = new Model('/sc/shopCommunications');
        $count = 0;
        foreach($shopIds as $shopId) {
            $model->insert([
                'shop_communication_id' => $lastInsertId,
                'mall_id'               => $this->getData('mz.mall.id'),
                'shop_id'               => $shopId,
                'title'                 => $this->input('title'),
                'message'               => $this->input('message'),
                'is_read'               => 0,
                'created_at'            => CURRENT_TIMESTAMP,
                'updated_at'            => CURRENT_TIMESTAMP
            ]);
            $count++;
        }

        (new Model('/sc/shopCommunication'))->update([
            'id'         => $lastInsertId,
            'shop_count' => $count
        ]);
        return $count;
    }

    public function getMemosByMemoId($memoId) {
        return (new Model('/sc/shopCommunications'))->loadDataSet('byCommunicationId')->commId($memoId)->runDataSet()->results;
    }

    public function deleteMemoById($memoId) {
        (new Model('/sc/shopCommunication'))->delete([
            'id' => $memoId
        ]);

        return (new Model('/sc/shopCommunications'))->delete([
            'shop_communication_id' => $memoId
        ]);
    }


    // **********************        TURNOVER       **************************
    public function getTurnoverMallIdAndYear($mallId,$year) {
        return (new Model('/sc/shop'))->loadDataSet('byMallAndTurnoverYear')->mallId($mallId)->year($year)->runDataSet()->results;
    }

    public function getTurnoverMallIdMonthAndYear($mallId,$year,$month) {
        return (new Model('/sc/shop'))->loadDataSet('byMallAndTurnoverMonthAndYear')->mallId($mallId)->year($year)->month($month)->runDataSet()->results;
    }


    // ***************        COMMUNICATION CREDITS       **************************
    public function getMallSMSCredits($mallId) {
        $smsCreditMold = Model::mold('/sc/clientCommCredits')->byTypeAndMallId([
            'mallId' => $mallId,
            'commType' => 1
        ]);
        return $smsCreditMold;
    }

    public function getMallEmailCredits($mallId) {
        $emailCreditMold = Model::mold('/sc/clientCommCredits')->byTypeAndMallId([
            'mallId' => $mallId,
            'commType' => 2
        ]);
        return $emailCreditMold;
    }

    public function getCommunicationMallContactLists($mallId) {
        return $contactListMold = Model::mold('/sc/clientCommLists')->byMallId([
            'mallId' => $mallId
        ])->getAll();
    }

    public function getCommCreditsByClientAndMall() {
        Model:mold('');
    }
}
