<?php
/**
 * Created by PhpStorm.
 * User: Anh Lai
 * Date: 2017/5/11 0011
 * Time: 下午 12:11
 */

namespace App\Http\Controllers;

use App\User;
use AuthUtils;
use Illuminate\Support\Facades\Auth;


class IndexPageController extends Controller
{


    public function __construct()
    {
        $this->middleware('base.check');
    }

    function __invoke()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->getIsLogin()) {
            return view('static.index.noLogin')->with('_USER', $user)->with("topNavValueText", "首页");
        }
        $authUtils = new AuthUtils();
        $authUtils->getAllAuth($user);
        if ($authUtils->getAuthCount() > 0) {
            if ($user->getUserShowAllInIndex() != 0 && $user->getUserShowAllInIndex() != false) {
                return view('static.index.allAuth')->with("_USER", $user)->with("topNavValueText", "首页")
                    ->with("authUtils", $authUtils)->with('authList', $authUtils->getAuthList())
                    ->with('pageUrl', "/");
            }
            return view('static.index.auth')->with("_USER", $user)->with("topNavValueText", "默认安全令")
                ->with("authUtils", $authUtils)->with('authBean', $authUtils->getDefaultAuth())
                ->with('fromGetDefault', true)
                ->with('pageUrl', "/");
        }
        $captchaCodeUnix = time();
        $maxAuthCount = $user->getUserDonated() == 1 ? config('app.auth_max_count_donated_user') : config('app.auth_max_count_standard_user');
        return view('static.index.add')->with("_USER", $user)->with("topNavValueText", "添加安全令")
            ->with("authUtils", $authUtils)->with('authBean', $authUtils->getDefaultAuth())
            ->with("captchaCodeUnix", $captchaCodeUnix)->with("maxAuthCount", $maxAuthCount);
    }
}