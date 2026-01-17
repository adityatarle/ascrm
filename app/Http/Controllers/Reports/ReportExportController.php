<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Livewire\Reports\ReportsPage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Dispatch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportExportController extends Controller
{
    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $format = $request->get('format', 'pdf');
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');
        $selectedDealer = $request->get('dealer');
        $selectedStatus = $request->get('status');

        $organization = Auth::user()->organization;

        if ($format === 'pdf') {
            return $this->exportPdf($type, $organization, $dateFrom, $dateTo, $selectedDealer, $selectedStatus);
        } else {
            return $this->exportExcel($type, $organization, $dateFrom, $dateTo, $selectedDealer, $selectedStatus);
        }
    }

    protected function exportPdf($type, $organization, $dateFrom, $dateTo, $selectedDealer, $selectedStatus)
    {
        $data = $this->getReportData($type, $dateFrom, $dateTo, $selectedDealer, $selectedStatus);
        
        $title = ucfirst(str_replace('_', ' ', $type)) . ' Report';
        
        $pdf = Pdf::loadView('reports.export-pdf', [
            'organization' => $organization,
            'title' => $title,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'type' => $type,
            'data' => $data,
        ]);

        $filename = str_replace(' ', '_', strtolower($title)) . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    protected function exportExcel($type, $organization, $dateFrom, $dateTo, $selectedDealer, $selectedStatus)
    {
        $data = $this->getReportData($type, $dateFrom, $dateTo, $selectedDealer, $selectedStatus);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set organization header
        $row = 1;
        $sheet->setCellValue('A' . $row, $organization->name ?? 'Organization');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        if ($organization->gstin) {
            $sheet->setCellValue('A' . $row, 'GSTIN: ' . $organization->gstin);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }
        
        if ($organization->address) {
            $sheet->setCellValue('A' . $row, $organization->address);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }
        
        $row++;
        $title = ucfirst(str_replace('_', ' ', $type)) . ' Report';
        $sheet->setCellValue('A' . $row, $title);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        if ($dateFrom && $dateTo) {
            $sheet->setCellValue('A' . $row, 'Period: ' . \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') . ' to ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y'));
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }
        
        $sheet->setCellValue('A' . $row, 'Generated On: ' . now()->format('d/m/Y h:i A'));
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row += 2;
        
        // Add data based on type
        $this->addExcelData($sheet, $type, $data, $row);
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = str_replace(' ', '_', strtolower($title)) . '_' . now()->format('Y-m-d') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    protected function getReportData($type, $dateFrom, $dateTo, $selectedDealer, $selectedStatus)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;

        $query = Order::where('organization_id', $organizationId)
            ->where('status', '!=', 'cancelled');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($selectedDealer) {
            $query->where('dealer_id', $selectedDealer);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        switch ($type) {
            case 'sales':
                $orders = $query->with('dealer')->get();
                return [
                    'total_revenue' => $orders->sum('grand_total'),
                    'total_orders' => $orders->count(),
                    'average_order_value' => $orders->count() > 0 ? $orders->sum('grand_total') / $orders->count() : 0,
                    'total_discount' => $orders->sum('discount_amount'),
                    'orders' => $orders->take(100),
                ];
                
            case 'orders':
                $orders = $query->with(['dealer', 'items.product'])->get();
                return [
                    'total_orders' => $orders->count(),
                    'by_status' => $orders->groupBy('status')->map->count(),
                    'orders' => $orders->take(100),
                ];
                
            case 'payments':
                $orders = $query->get();
                $orderIds = $orders->pluck('id');
                $payments = Payment::whereIn('order_id', $orderIds)->with(['order.dealer'])->get();
                return [
                    'total_paid' => $payments->where('status', 'completed')->sum('amount'),
                    'total_pending' => $orders->sum(function($order) {
                        return max(0, $order->grand_total - $order->paid_amount);
                    }),
                    'payments' => $payments->take(100),
                ];
                
            case 'products':
                $orders = $query->with(['items.product'])->get();
                $productData = $orders->flatMap(function($order) {
                    return $order->items;
                })->groupBy('product_id')->map(function($items) {
                    return [
                        'product' => $items->first()->product,
                        'total_quantity' => $items->sum('quantity'),
                        'total_revenue' => $items->sum('subtotal'),
                        'order_count' => $items->groupBy('order_id')->count(),
                    ];
                })->sortByDesc('total_revenue');
                return [
                    'top_products' => $productData->take(100),
                ];
                
            case 'dealers':
                $orders = $query->with('dealer')->get();
                $dealerData = $orders->groupBy('dealer_id')->map(function($dealerOrders) {
                    return [
                        'dealer' => $dealerOrders->first()->dealer,
                        'order_count' => $dealerOrders->count(),
                        'total_revenue' => $dealerOrders->sum('grand_total'),
                        'average_order_value' => $dealerOrders->count() > 0 
                            ? $dealerOrders->sum('grand_total') / $dealerOrders->count() 
                            : 0,
                    ];
                })->sortByDesc('total_revenue');
                return [
                    'top_dealers' => $dealerData->take(100),
                ];
                
            case 'dispatches':
                $orders = $query->get();
                $orderIds = $orders->pluck('id');
                $dispatches = Dispatch::whereIn('order_id', $orderIds)
                    ->with(['order.dealer', 'items'])
                    ->get();
                return [
                    'total_dispatches' => $dispatches->count(),
                    'dispatches' => $dispatches->take(100),
                ];
                
            case 'gst':
                $orders = $query->with(['dealer.state'])->get();
                return [
                    'total_cgst' => $orders->sum('cgst_amount'),
                    'total_sgst' => $orders->sum('sgst_amount'),
                    'total_igst' => $orders->sum('igst_amount'),
                    'total_gst' => $orders->sum(function($order) {
                        return $order->cgst_amount + $order->sgst_amount + $order->igst_amount;
                    }),
                    'by_state' => $orders->groupBy(function($order) {
                        return $order->dealer->state->name ?? 'Unknown';
                    })->map(function($stateOrders) {
                        return [
                            'cgst' => $stateOrders->sum('cgst_amount'),
                            'sgst' => $stateOrders->sum('sgst_amount'),
                            'igst' => $stateOrders->sum('igst_amount'),
                            'count' => $stateOrders->count(),
                        ];
                    }),
                ];
                
            default:
                return [];
        }
    }

    protected function addExcelData($sheet, $type, $data, &$row)
    {
        switch ($type) {
            case 'sales':
                $sheet->setCellValue('A' . $row, 'Order Number');
                $sheet->setCellValue('B' . $row, 'Dealer');
                $sheet->setCellValue('C' . $row, 'Subtotal');
                $sheet->setCellValue('D' . $row, 'Discount');
                $sheet->setCellValue('E' . $row, 'GST');
                $sheet->setCellValue('F' . $row, 'Total');
                $this->styleHeaderRow($sheet, $row);
                $row++;
                
                foreach ($data['orders'] as $order) {
                    $sheet->setCellValue('A' . $row, $order->order_number);
                    $sheet->setCellValue('B' . $row, $order->dealer->name ?? 'N/A');
                    $sheet->setCellValue('C' . $row, $order->subtotal);
                    $sheet->setCellValue('D' . $row, $order->discount_amount);
                    $sheet->setCellValue('E' . $row, $order->cgst_amount + $order->sgst_amount + $order->igst_amount);
                    $sheet->setCellValue('F' . $row, $order->grand_total);
                    $row++;
                }
                break;
                
            case 'orders':
                $sheet->setCellValue('A' . $row, 'Order Number');
                $sheet->setCellValue('B' . $row, 'Dealer');
                $sheet->setCellValue('C' . $row, 'Amount');
                $sheet->setCellValue('D' . $row, 'Status');
                $sheet->setCellValue('E' . $row, 'Items');
                $sheet->setCellValue('F' . $row, 'Date');
                $this->styleHeaderRow($sheet, $row);
                $row++;
                
                foreach ($data['orders'] as $order) {
                    $sheet->setCellValue('A' . $row, $order->order_number);
                    $sheet->setCellValue('B' . $row, $order->dealer->name ?? 'N/A');
                    $sheet->setCellValue('C' . $row, $order->grand_total);
                    $sheet->setCellValue('D' . $row, ucfirst($order->status));
                    $sheet->setCellValue('E' . $row, $order->items->count());
                    $sheet->setCellValue('F' . $row, $order->created_at->format('d/m/Y'));
                    $row++;
                }
                break;
                
            case 'payments':
                $sheet->setCellValue('A' . $row, 'Payment #');
                $sheet->setCellValue('B' . $row, 'Order #');
                $sheet->setCellValue('C' . $row, 'Dealer');
                $sheet->setCellValue('D' . $row, 'Amount');
                $sheet->setCellValue('E' . $row, 'Mode');
                $sheet->setCellValue('F' . $row, 'Status');
                $this->styleHeaderRow($sheet, $row);
                $row++;
                
                foreach ($data['payments'] as $payment) {
                    $sheet->setCellValue('A' . $row, $payment->id);
                    $sheet->setCellValue('B' . $row, $payment->order->order_number ?? 'N/A');
                    $sheet->setCellValue('C' . $row, $payment->order->dealer->name ?? 'N/A');
                    $sheet->setCellValue('D' . $row, $payment->amount);
                    $sheet->setCellValue('E' . $row, ucfirst($payment->payment_mode));
                    $sheet->setCellValue('F' . $row, ucfirst($payment->status));
                    $row++;
                }
                break;
                
            case 'products':
                $sheet->setCellValue('A' . $row, '#');
                $sheet->setCellValue('B' . $row, 'Product');
                $sheet->setCellValue('C' . $row, 'Quantity');
                $sheet->setCellValue('D' . $row, 'Revenue');
                $sheet->setCellValue('E' . $row, 'Orders');
                $this->styleHeaderRow($sheet, $row);
                $row++;
                
                $index = 1;
                foreach ($data['top_products'] as $item) {
                    $sheet->setCellValue('A' . $row, $index++);
                    $sheet->setCellValue('B' . $row, $item['product']->name ?? 'N/A');
                    $sheet->setCellValue('C' . $row, $item['total_quantity']);
                    $sheet->setCellValue('D' . $row, $item['total_revenue']);
                    $sheet->setCellValue('E' . $row, $item['order_count']);
                    $row++;
                }
                break;
                
            case 'dealers':
                $sheet->setCellValue('A' . $row, '#');
                $sheet->setCellValue('B' . $row, 'Dealer');
                $sheet->setCellValue('C' . $row, 'Orders');
                $sheet->setCellValue('D' . $row, 'Revenue');
                $sheet->setCellValue('E' . $row, 'Avg Order Value');
                $this->styleHeaderRow($sheet, $row);
                $row++;
                
                $index = 1;
                foreach ($data['top_dealers'] as $item) {
                    $sheet->setCellValue('A' . $row, $index++);
                    $sheet->setCellValue('B' . $row, $item['dealer']->name ?? 'N/A');
                    $sheet->setCellValue('C' . $row, $item['order_count']);
                    $sheet->setCellValue('D' . $row, $item['total_revenue']);
                    $sheet->setCellValue('E' . $row, $item['average_order_value']);
                    $row++;
                }
                break;
                
            case 'dispatches':
                $sheet->setCellValue('A' . $row, 'Dispatch #');
                $sheet->setCellValue('B' . $row, 'Order #');
                $sheet->setCellValue('C' . $row, 'Dealer');
                $sheet->setCellValue('D' . $row, 'LR Number');
                $sheet->setCellValue('E' . $row, 'Items');
                $sheet->setCellValue('F' . $row, 'Status');
                $this->styleHeaderRow($sheet, $row);
                $row++;
                
                foreach ($data['dispatches'] as $dispatch) {
                    $sheet->setCellValue('A' . $row, $dispatch->id);
                    $sheet->setCellValue('B' . $row, $dispatch->order->order_number ?? 'N/A');
                    $sheet->setCellValue('C' . $row, $dispatch->order->dealer->name ?? 'N/A');
                    $sheet->setCellValue('D' . $row, $dispatch->lr_number ?? 'N/A');
                    $sheet->setCellValue('E' . $row, $dispatch->items->sum('quantity'));
                    $sheet->setCellValue('F' . $row, ucfirst(str_replace('_', ' ', $dispatch->status)));
                    $row++;
                }
                break;
                
            case 'gst':
                $sheet->setCellValue('A' . $row, 'State');
                $sheet->setCellValue('B' . $row, 'CGST');
                $sheet->setCellValue('C' . $row, 'SGST');
                $sheet->setCellValue('D' . $row, 'IGST');
                $sheet->setCellValue('E' . $row, 'Total GST');
                $sheet->setCellValue('F' . $row, 'Orders');
                $this->styleHeaderRow($sheet, $row);
                $row++;
                
                foreach ($data['by_state'] as $state => $stateData) {
                    $sheet->setCellValue('A' . $row, $state);
                    $sheet->setCellValue('B' . $row, $stateData['cgst']);
                    $sheet->setCellValue('C' . $row, $stateData['sgst']);
                    $sheet->setCellValue('D' . $row, $stateData['igst']);
                    $sheet->setCellValue('E' . $row, $stateData['cgst'] + $stateData['sgst'] + $stateData['igst']);
                    $sheet->setCellValue('F' . $row, $stateData['count']);
                    $row++;
                }
                break;
        }
    }

    protected function styleHeaderRow($sheet, $row)
    {
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '267b3f']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }
}
