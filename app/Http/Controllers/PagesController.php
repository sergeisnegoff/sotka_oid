<?php

namespace App\Http\Controllers;

use App\Models\AdvantagesModel;
use App\Models\AdvProductsCategory;
use App\Models\BlockquoteModel;
use App\Models\BrandsModel;
use App\Models\CertificateModel;
use App\Models\ContactsManagersModel;
use App\Models\ContactsSupervisorModel;
use App\Models\CountersModel;
use App\Models\FAQModel;
use App\Models\OurGoals;
use App\Models\OurLifeModel;
use App\Models\PagesModel;
use App\Models\PriceListsModel;

class PagesController extends Controller
{
    public function index($id=0) {
    	$slug = \Illuminate\Support\Facades\Request::segment(1);

    	switch ($slug) {
		    case 'about-us':
		    	return $this->aboutUs();
		    case 'delivery':
			    return $this->delivery();
		    case 'our-life':
			    return $this->ourLife($id);
		    case 'price-lists':
		    	return $this->priceLists();
		    case 'contacts':
                return $this->contacts();
            case 'reklama':
                return $this->advProducts();
            default:
                return $this->pages($slug);
	    }
    }

    public function aboutUs() {
    	$data['info'] = PagesModel::where('slug', 'like' ,'%about-us%')->first();
		$data['ourGoals'] = OurGoals::all();
		$data['blockQuote'] = BlockquoteModel::where('is_active', 1)->first();
		$data['counters'] = CountersModel::all();
		$data['certificates'] = CertificateModel::all();
		$data['advantages'] = AdvantagesModel::all();
		$data['brands'] = BrandsModel::all();
    	return view('pages.about', $data);
    }

    public function delivery() {
	    $data['info'] = PagesModel::where('slug', 'like' ,'%delivery%')->first();
	    $data['faq'] = FAQModel::orderBy('sorder')->get();
	    return view('pages.delivery', $data);
    }

    public function ourLife($id) {
    	if ((int)$id == 0) {
		    $data['info'] = PagesModel::where('slug', 'like', '%our-life%')->first();
		    $data['ourLife'] = OurLifeModel::all();
		    return view('pages.life.index', $data);
	    } else {
		    $data['info'] = PagesModel::where('slug', 'like', '%our-life%')->first();
		    $data['detailInfo'] = OurLifeModel::where(['id' => $id])->first();
		    return view('pages.life.detail', $data);
	    }
    }

    public function priceLists() {
	    $data['info'] = PagesModel::where('slug', 'like' ,'%price-lists%')->first();
	    $data['priceLists'] = PriceListsModel::orderBy('sorder')->get();

	    return view('pages.priceList', $data);
    }

    public function contacts() {
        $data['info'] = PagesModel::where('slug', 'like' ,'%contacts%')->first();
        $data['contactsManagers'] = ContactsManagersModel::where('visible', 1)->get();
        $data['contactsSupervisor'] = ContactsSupervisorModel::all();

        return view('pages.contacts', $data);
    }

    public function pages($slug) {
        $data['info'] = PagesModel::where('slug', 'like' ,'%'.$slug.'%')->first();
        if (empty($data['info']))
            abort(404);
        return view('pages.default', $data);
    }

    public function advProducts() {
        $data['info'] = PagesModel::where('slug', 'like', '%reklama%')->first();
        $data['items'] = AdvProductsCategory::getContent();

        return view('pages.advProducts', $data);
    }
}
