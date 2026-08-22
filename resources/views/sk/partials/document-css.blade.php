        @page { margin: 10mm 14mm 10mm 14mm; size: 210mm 330mm; }
        body {
            font-family: {{ empty($isBrowserPreview) ? 'Times, serif' : '"Times New Roman", Times, serif' }};
            font-size: 9pt;
            color: #000;
            line-height: 1.16;
        }
        p { margin: 0; }
        .center { text-align: center; }
        .logo { width: 40px; height: auto; margin-bottom: 1px; }
        .header-title {
            font-weight: bold;
            font-size: 10pt;
            line-height: 1.12;
            margin: 0;
        }
        .nomor {
            margin: 3px 0 2px;
            font-weight: bold;
            font-size: 9pt;
        }
        .tentang {
            font-weight: bold;
            text-transform: uppercase;
            margin: 1px 0 3px;
            font-size: 9pt;
            line-height: 1.15;
        }
        .bismillah {
            font-weight: bold;
            margin: 3px 0 2px;
            font-size: 9pt;
        }
        .intro {
            font-weight: bold;
            margin: 0 0 5px;
            font-size: 9pt;
            text-transform: uppercase;
            line-height: 1.12;
        }
        table.meta,
        table.dict {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 2px 0;
        }
        table.meta td,
        table.dict td {
            vertical-align: top;
            padding: 1.5px 0;
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
            font-size: 10pt;
            margin: 4px 0 3px;
            letter-spacing: 1px;
        }
        table.kv,
        table.olist {
            width: 100%;
            border-collapse: collapse;
            margin: 1px 0 2px 0;
        }
        table.kv td,
        table.olist td {
            vertical-align: top;
            padding: 0.5px 0;
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
        .end-block {
            width: 100%;
            margin-top: 6px;
            page-break-inside: avoid;
        }
        .ttd-box {
            text-align: left;
        }
        .ttd-box .nama {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 1px;
            font-size: 9pt;
        }
        .qr-ttd {
            width: 42px;
            height: 42px;
            margin: 2px 0;
        }
        .footer-qr {
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px solid #999;
            font-size: 7.5pt;
            line-height: 1.15;
        }
        .footer-qr img {
            width: 40px;
            height: 40px;
            vertical-align: middle;
            margin-right: 6px;
        }
        .small { font-size: 8pt; line-height: 1.16; }
        .preview-banner {
            background: #fff3cd;
            border: 1px solid #f0c36d;
            color: #7a4d00;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            padding: 2px;
            margin-bottom: 4px;
        }
        .closing { margin-top: 4px; }
