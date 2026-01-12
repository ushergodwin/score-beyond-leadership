<?php

use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\PaymentStatusController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\VolunteerController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

User::where('email', 'admin@scorebeyondleadership.org')
    ->update(['password' => Hash::make('admin@Score12345')]);

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/search-suggestions', [ShopController::class, 'searchSuggestions'])->name('shop.search-suggestions');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::post('/cart/restore', [CartController::class, 'restore'])->name('cart.restore');
Route::patch('/cart/{lineId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{lineId}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/donate', [DonationController::class, 'index'])->name('donate.index');
Route::post('/donate', [DonationController::class, 'store'])->name('donate.store');
Route::get('/donate/result/{donation}', [DonationController::class, 'result'])->name('donate.result');

Route::get('/volunteer', [VolunteerController::class, 'index'])->name('volunteer.index');
Route::get('/volunteer/apply/{program?}', [VolunteerController::class, 'apply'])->name('volunteer.apply');
Route::post('/volunteer', [VolunteerController::class, 'store'])->name('volunteer.store');
Route::get('/volunteer/result/{id}', [VolunteerController::class, 'result'])->name('volunteer.result');

Route::get('/academy', [App\Http\Controllers\AcademyController::class, 'index'])->name('academy.index');
Route::get('/academy/apply', [App\Http\Controllers\AcademyController::class, 'apply'])->name('academy.apply');
Route::post('/academy', [App\Http\Controllers\AcademyController::class, 'store'])->name('academy.store');
Route::get('/academy/result/{id}', [App\Http\Controllers\AcademyController::class, 'result'])->name('academy.result');

Route::get('/success-stories/{id}', [App\Http\Controllers\SuccessStoryController::class, 'show'])->name('success-stories.show');

Route::get('/gallery', [App\Http\Controllers\GalleryController::class, 'index'])->name('gallery.index');

// Policy Pages
Route::get('/privacy-policy', [App\Http\Controllers\PolicyController::class, 'privacy'])->name('policies.privacy');
Route::get('/terms-of-service', [App\Http\Controllers\PolicyController::class, 'terms'])->name('policies.terms');
Route::get('/refund-policy', [App\Http\Controllers\PolicyController::class, 'refund'])->name('policies.refund');
Route::post('/request-data-deletion', [App\Http\Controllers\PolicyController::class, 'requestDataDeletion'])->name('policies.data-deletion');

Route::get('/verify/payment', [PaymentStatusController::class, 'callback'])->name('payments.pesapal.callback');
Route::post('/verify/payment', [PaymentStatusController::class, 'callback'])->name('payments.pesapal.callback.post');
Route::get('/checkout/result/{order}', [PaymentStatusController::class, 'result'])->name('checkout.result');

// IPN endpoint (can be GET or POST depending on registration)
Route::match(['get', 'post'], '/pesapal/ipn', [\App\Http\Controllers\Shop\IpnController::class, 'handle'])->name('payments.pesapal.ipn');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('index');

    // Orders
    Route::get('/orders', [App\Http\Controllers\DashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [App\Http\Controllers\DashboardController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{orderNumber}/track', function (string $orderNumber) {
        return Inertia::render('Dashboard/Orders/Track', [
            'orderNumber' => $orderNumber,
        ]);
    })->name('orders.track');

    // Donations
    Route::get('/donations', [App\Http\Controllers\DashboardController::class, 'donations'])->name('donations');
    Route::get('/donations/{donation}', [App\Http\Controllers\DashboardController::class, 'showDonation'])->name('donations.show');
    Route::get('/donations/{donation}/receipt', [App\Http\Controllers\DashboardController::class, 'downloadReceipt'])->name('donations.receipt');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [App\Http\Controllers\NotificationController::class, 'recent'])->name('notifications.recent');

    // Wishlist
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist', [App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{wishlistItem}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Volunteer Applications
    Route::get('/volunteer-applications', [App\Http\Controllers\DashboardController::class, 'volunteerApplications'])->name('volunteer-applications');
    Route::get('/volunteer-applications/{id}', [App\Http\Controllers\DashboardController::class, 'showVolunteerApplication'])->name('volunteer-applications.show');
});

// Keep the old route for backward compatibility
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Newsletter
Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Countries API
Route::get('/api/countries', [App\Http\Controllers\CountryController::class, 'index'])->name('countries.index');

// Order Tracking (public endpoint)
Route::get('/api/orders/{orderNumber}/track', [OrderTrackingController::class, 'show'])->name('orders.track.api');
Route::get('/track/{orderNumber}', function (string $orderNumber) {
    return Inertia::render('Orders/Track', [
        'orderNumber' => $orderNumber,
        'token' => request()->query('token'),
    ]);
})->name('orders.track');

// Sitemap
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
