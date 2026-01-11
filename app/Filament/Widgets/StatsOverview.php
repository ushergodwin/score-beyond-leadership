<?php

namespace App\Filament\Widgets;

use App\Models\AcademyApplication;
use App\Models\Customer;
use App\Models\Donation;
use App\Models\Order;
use App\Models\Product;
use App\Models\VolunteerApplication;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'completed')->sum('grand_total');
        $pendingOrders = Order::where('status', 'pending')->count();
        
        $totalDonations = Donation::count();
        $totalDonationAmount = Donation::where('payment_status', 'completed')->sum('amount');
        $pendingDonations = Donation::where('payment_status', 'pending')->count();
        
        $totalProducts = Product::count();
        $publishedProducts = Product::where('status', 'published')->count();
        $lowStockProducts = Product::where('inventory', '<=', 5)->count();
        
        $totalCustomers = Customer::count();
        $totalVolunteers = VolunteerApplication::count();
        $pendingVolunteers = VolunteerApplication::whereIn('status', ['submitted', 'pending'])->count();
        
        $totalAcademyApplications = AcademyApplication::count();
        $pendingAcademyApplications = AcademyApplication::whereIn('status', ['submitted', 'pending'])->count();
        $approvedAcademyApplications = AcademyApplication::where('status', 'approved')->count();

        return [
            // E-commerce Stats
            Stat::make('Total Orders', $totalOrders)
                ->description($pendingOrders . ' pending')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            Stat::make('Total Revenue', 'UGX ' . number_format($totalRevenue, 0))
                ->description('From completed orders')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            Stat::make('Total Products', $totalProducts)
                ->description($publishedProducts . ' published')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info'),
            Stat::make('Low Stock Products', $lowStockProducts)
                ->description('Need restocking')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'danger' : 'success'),
            
            // Donations Stats
            Stat::make('Total Donations', $totalDonations)
                ->description('UGX ' . number_format($totalDonationAmount, 0) . ' received')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),
            Stat::make('Pending Donations', $pendingDonations)
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            // Applications Stats
            Stat::make('Volunteer Applications', $totalVolunteers)
                ->description($pendingVolunteers . ' pending review')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('Academy Applications', $totalAcademyApplications)
                ->description($pendingAcademyApplications . ' pending, ' . $approvedAcademyApplications . ' approved')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),
            
            // Users Stats
            Stat::make('Total Customers', $totalCustomers)
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}

