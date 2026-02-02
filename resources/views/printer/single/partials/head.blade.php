<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{env('APP_NAME')}}</title>
    <style>
        :root{
            --ink:#111;
            --muted:#666;
            --soft:#9aa0a6;
            --line:#d9d9d9;
            --brand:#111;
        }
        *{box-sizing:border-box}
        html,body{height:100%}
        body{
            margin:0;display:grid;place-items:center;background:#f3f4f6;color:var(--ink);
            font:12px/1.3 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,Helvetica,system-ui,sans-serif;
        }
        .receipt-wrap{padding:8px}

        .receipt{
            width:320px;
            background:#fff; color:var(--ink);
            overflow:hidden; border:1px solid #eee;
        }
        .paper{padding:8px 12px 8px}

        .brand{font-weight:800; letter-spacing:.4px; text-align:center; font-size:20px; margin-bottom:4px;}
        .meta{display:flex; justify-content:space-between; gap:6px; font-size:11px; color:var(--muted); padding:4px 0 2px}
        .meta .pill{display:flex; align-items:center; gap:4px;font-weight: bold}
        .meta .hourly{display:flex;color: black; align-items:center; gap:4px;font-weight: bold;font-size: 15px}
        .icon{width:16px;height:16px;border:1.5px solid var(--ink);border-radius:50%;display:inline-grid;place-items:center;font-size:9px;text-align:center;justify-content:center;align-items:center}
        .icon.check{border-color:var(--ink);margin:0 auto 4px; display:block}

        .divider{height:1px;background:var(--line);margin:6px 0}

        .order-no{font-size:14px; font-weight:800}
        .customer{font-size:12px; color:var(--muted)}

        .note{margin:4px 0 2px;text-align: center; padding:4px 6px;font-size:13px;font-weight: bold}
        .note.payment-type{margin-top:2px}

        .total{
            color: black;font-weight: bold;
        }

        .list{padding:4px 0}
        .row{display:flex; align-items:flex-end; justify-content:space-between; gap:6px; padding:4px 0}
        .row + .row{border-top:1px dotted var(--line)}
        .item-title{font-weight:bold;margin-bottom: 5px;margin-top: 2px;font-size:16px;}
        .sub{color:black; font-size:15px; padding-left:8px; margin-top:2px;font-weight: bold}

        .line{border-top:2px solid var(--ink); margin-top:4px; padding-top:4px}
        .fine-line{border-top:1px solid var(--ink); margin-top:4px; padding-top:4px}

        .sum{border-top:2px solid var(--ink); margin-top:4px; padding-top:4px}
        .sum .row{border-top:0; font-size:12px}
        .sum .label{font-weight:600;color: black;font-size: 14px}
        .grand{font-size:16px; font-weight:800;}
        .vat{font-size:11px; color:var(--ink);font-weight: bold}

        .footer{padding:6px 0 10px; text-align:center; font-size:11px; color:var(--muted)}
        .shop{font-weight:700; color:var(--ink)}
        .footer .order-date{margin-bottom:4px;font-weight: bold}
        .footer .order-no-footer{font-size:10px;font-weight: bold}
        .footer .thank{margin-top:6px;font-weight: 700}

        @media print {
            @page {
                size: 80mm 297mm;
                margin: 0;
            }
        }
        .text-bold {font-weight: bold; color: var(--ink);}
        .logo {
            height:45px;
            width:45px;
            filter: grayscale(100%);
        }
    </style>
</head>
