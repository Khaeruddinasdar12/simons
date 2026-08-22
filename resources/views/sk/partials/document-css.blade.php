        @page { margin: 12mm 14mm 14mm 14mm; size: 210mm 330mm; }
        body {
            font-family: {{ empty($isBrowserPreview) ? 'Times, serif' : '"Times New Roman", Times, serif' }};
            font-size: 9.5pt;
            color: #000;
            line-height: 1.28;
        }
        p { margin: 0; }
        .center { text-align: center; }
        .logo { width: 52px; height: auto; margin-bottom: 2px; }
        .header-title {
            font-weight: bold;
            font-size: 10.5pt;
            line-height: 1.15;
            margin: 0;
        }
        .nomor {
            margin: 6px 0 3px;
            font-weight: bold;
            font-size: 9.5pt;
        }
        .tentang {
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0 6px;
            font-size: 9.5pt;
            line-height: 1.2;
        }
        .bismillah {
            font-weight: bold;
            margin: 6px 0 3px;
            font-size: 9.5pt;
        }
        .intro {
            font-weight: bold;
            margin: 0 0 10px;
            font-size: 9.5pt;
            text-transform: uppercase;
            line-height: 1.2;
        }
        table.meta,
        table.dict {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 6px 0;
        }
        table.meta td,
        table.dict td {
            vertical-align: top;
            padding: 3px 0;
            text-align: left;
        }
        table.meta td.label,
        table.dict td.dict-key {
            width: 120px;
            font-weight: bold;
            white-space: nowrap;
            padding-right: 4px;
        }
        table.meta td.colon,
        table.dict td.dict-colon {
            width: 14px;
        }
        .bold { font-weight: bold; }
        .memutuskan {
            text-align: center;
            font-weight: bold;
            font-size: 10.5pt;
            margin: 8px 0 6px;
            letter-spacing: 1px;
        }
        table.kv,
        table.olist {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0 3px 0;
        }
        table.kv td,
        table.olist td {
            vertical-align: top;
            padding: 1px 0;
            text-align: left;
        }
        table.kv td.k {
            width: 110px;
            white-space: nowrap;
        }
        table.kv td.c {
            width: 14px;
        }
        table.olist td.n {
            width: 18px;
            white-space: nowrap;
        }
        .ttd-wrap { width: 100%; margin-top: 12px; }
        .ttd-box {
            width: 46%;
            float: right;
            text-align: left;
        }
        .ttd-box .nama {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 2px;
            font-size: 9.5pt;
        }
        .qr-ttd {
            width: 58px;
            height: 58px;
            margin: 3px 0;
        }
        .footer-qr {
            clear: both;
            margin-top: 10px;
            padding-top: 4px;
            border-top: 1px solid #999;
            font-size: 7.5pt;
        }
        .footer-qr img {
            width: 52px;
            height: 52px;
            vertical-align: middle;
            margin-right: 6px;
        }
        .small { font-size: 8pt; line-height: 1.25; }
        .clear { clear: both; }
        .preview-banner {
            background: #fff3cd;
            border: 1px solid #f0c36d;
            color: #7a4d00;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            padding: 3px;
            margin-bottom: 8px;
        }
        .closing { margin-top: 8px; }
