<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class OrdersExport implements FromCollection, WithHeadings, WithEvents, WithColumnFormatting, ShouldAutoSize
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return Order::select(['id', 'billing_name', 'billing_email', 'billing_subtotal', 'billing_discount', 'billing_total', 'created_at'])
      ->orderBy('id', 'desc')
      ->get();
  }

  public function headings(): array
  {
    return [
      '#',
      'Tên',
      'Email',
      'Tạm tính',
      'Giảm giá',
      'Tổng',
      'Ngày'
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        /* Apply default style for all cells */
        $styleArray = [
          'font' => [
            'name' => 'Arial',
            'size' => 9,
          ]
        ];

        $event->sheet->getDelegate()
          ->getStyle($event->sheet->calculateWorksheetDimension())
          ->applyFromArray($styleArray);
        /* freeze header */
        $event->sheet->getDelegate()->freezePane('A2');

        $styleFirstRow = [
          'font' => [
            'bold' => true,
            'color' => [
              'argb' => 'ffffff'
            ],
          ],

          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => [
              'argb' => '27ae60',
            ],
          ],
        ];
        $event->sheet->getDelegate()->getStyle('A1:G1')->applyFromArray($styleFirstRow);
      },
    ];
  }

  public function columnFormats(): array
  {
    return [
      'D' => '#,##0.00_-"đ"',
      'E' => '#,##0.00_-"đ"',
      'F' => '#,##0.00_-"đ"',
    ];
  }
}
