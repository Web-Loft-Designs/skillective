<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;
use App\Repositories\BookingRepository;

class BookingsPaymentsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
	private BookingRepository $bookingsRepository;
    private Request $request;

    public function __construct(BookingRepository $bookingsRepo, Request $request)
    {
        $this->request			= $request;
		$this->bookingsRepository = $bookingsRepo;

		$this->request->request->remove('page');
		$this->request->request->add(['limit' => -1]);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
        ];
    }

    public function headings() : array
    {
        $headings = ['ID'];

        $headings = array_merge($headings, [
        	'Payer',
        	'Recipient',
            'Date',
            'Last update',
            'Status',
            'Amount',
            'Total Fees'
            ]);

        return $headings;
    }

	public function map($booking): array
	{
		$map = [$booking->id];
		$map[] = $booking->student->getName();
		$map[] = $booking->instructor->getName();
		$map[] = $booking->transaction_created_at; // Date::dateTimeToExcel(
		$map[] = $booking->updated_at;
		$map[] = Booking::getStatusTitle($booking->status);
		$map[] = $booking->spot_price - $booking->service_fee - $booking->processor_fee - $booking->virtual_fee;
		$map[] = $booking->service_fee + $booking->processor_fee + $booking->virtual_fee;

		return $map;
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->bookingsRepository->getBookings($this->request);
    }
}
