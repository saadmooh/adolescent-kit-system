<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قائمة حضور تدريب حقيبة الابتكار والتعبير لليافعين</title>
    <style>
        @page {
            size: A4;
            margin: 1.2cm;
        }
        body {
            font-family: 'Times New Roman', Tahoma, Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            line-height: 1.3;
        }
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 15pt;
            margin-bottom: 12px;
            text-decoration: underline;
        }
        .meta-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 11pt;
            font-weight: bold;
        }
        .notice {
            font-size: 10pt;
            margin-bottom: 8px;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            font-size: 10pt;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer-signatures {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 11pt;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #000000;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 0px;
            border: 2px solid #000000;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 4px 4px 0px 0px rgba(0,0,0,1);
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn no-print">🖨️ طباعة كشف الحضور (Print)</button>

    <div class="header-title">
        قائمة الحضور تدريب حقيبة الابتكار والتعبير لليافعين Kit Adolescent
    </div>

    <div class="meta-line">
        <span>المؤسسة: <strong>{{ $session->institution->name }}</strong></span>
        <span>بلدية: <strong>{{ $session->municipality }}</strong></span>
        <span>يوم: <strong>{{ $session->session_date->format('d/m/Y') }}</strong></span>
    </div>

    <div class="notice">
        يرجى ملء الخانات كالتالي:
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 6%;">الرقم</th>
                <th rowspan="2" style="width: 25%;">الاسم واللقب</th>
                <th rowspan="2" style="width: 15%;">تاريخ الميلاد</th>
                <th rowspan="2" style="width: 14%;">البلدية</th>
                <th rowspan="2" style="width: 14%;">المستوى الدراسي</th>
                <th colspan="2" style="width: 12%;">الجنس</th>
                <th rowspan="2" style="width: 14%;">المرض المزمن</th>
            </tr>
            <tr>
                <th style="width: 6%;">أنثى</th>
                <th style="width: 6%;">ذكر</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $count = $attendances->count();
                $totalRows = max(70, $count);
            @endphp
            @for($i = 0; $i < $totalRows; $i++)
                @php $att = $attendances[$i] ?? null; @endphp
                <tr>
                    <td>{{ sprintf('%02d', $i + 1) }}</td>
                    <td class="text-right">{{ $att ? $att->beneficiary->full_name : '' }}</td>
                    <td>{{ $att ? $att->beneficiary->birth_date->format('d/m/Y') : '' }}</td>
                    <td>{{ $att ? $att->beneficiary->municipality : '' }}</td>
                    <td>{{ $att ? $att->beneficiary->education_level : '' }}</td>
                    <td>{{ ($att && $att->beneficiary->gender === 'أنثى') ? 'X' : '' }}</td>
                    <td>{{ ($att && $att->beneficiary->gender === 'ذكر') ? 'X' : '' }}</td>
                    <td class="text-right">
                        @if($att && $att->beneficiary->has_chronic_disease)
                            {{ $att->beneficiary->chronic_disease_details ?: 'نعم' }}
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer-signatures">
        <div>
            الميسر : <strong>{{ $session->leadFacilitator1->name ?? '.....................................' }}</strong>
        </div>
        <div>
            المساعدون : 
            @php $assts = $session->assistant_facilitators ?? []; @endphp
            <strong>{{ !empty($assts) ? implode(' - ', $assts) : '.....................................' }}</strong>
        </div>
        <div>
            مصادقة المؤسسة / <strong>.....................................</strong>
        </div>
    </div>

</body>
</html>
