        @page { margin: 10mm 16mm 10mm 16mm; size: 210mm 330mm; }
        body {
            font-family: {{ empty($isBrowserPreview) ? 'Times, serif' : '"Times New Roman", Times, serif' }};
            font-size: 9.5pt;
            color: #000;
            line-height: 1.18;
        }
        p { margin: 0; }
        .center { text-align: center; }
        .logo { width: 48px; height: auto; margin-bottom: 2px; }
        .header-title {
            font-weight: bold;
            font-size: 10.5pt;
            line-height: 1.14;
            margin: 0;
        }
        .nomor {
            margin: 4px 0 2px;
            font-weight: bold;
            font-size: 9.5pt;
        }
        .tentang {
            font-weight: bold;
            text-transform: uppercase;
            margin: 1px 0 4px;
            font-size: 9.5pt;
            line-height: 1.15;
        }
        .bismillah {
            font-weight: bold;
            margin: 4px 0 2px;
            font-size: 9.5pt;
        }
        .intro {
            font-weight: bold;
            margin: 0 0 6px;
            font-size: 9.5pt;
            text-transform: uppercase;
            line-height: 1.14;
        }
        table.meta,
        table.dict {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            margin: 0 0 2px 0;
        }
        table.meta td,
        table.dict td {
            vertical-align: top;
            padding: 2px 0;
            text-align: left;
        }
        table.meta td.label,
        table.dict td.dict-key {
            width: 1%;
            font-weight: bold;
            white-space: nowrap;
            padding-right: 4px;
        }
        table.meta td.colon,
        table.dict td.dict-colon {
            width: 1%;
            white-space: nowrap;
            padding: 0 6px 0 2px;
            text-align: left;
        }
        .bold { font-weight: bold; }
        .memutuskan {
            text-align: center;
            font-weight: bold;
            font-size: 10.5pt;
            margin: 5px 0 3px;
            letter-spacing: 1px;
        }
        table.kv,
        table.olist {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            margin: 1px 0 2px 0;
        }
        table.kv td,
        table.olist td {
            vertical-align: top;
            padding: 1px 0;
            text-align: left;
        }
        table.kv td.k {
            width: 1%;
            white-space: nowrap;
            padding-right: 4px;
        }
        table.kv td.c {
            width: 1%;
            white-space: nowrap;
            padding: 0 6px 0 2px;
            text-align: left;
        }
        table.olist td.n {
            width: 1%;
            white-space: nowrap;
            padding-right: 6px;
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
            margin-top: 2px;
            font-size: 9.5pt;
        }
        .qr-ttd {
            width: 48px;
            height: 48px;
            margin: 2px 0;
        }
        .footer-qr {
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px solid #999;
            font-size: 8pt;
            line-height: 1.18;
        }
        .footer-qr img {
            width: 44px;
            height: 44px;
            vertical-align: middle;
            margin-right: 8px;
        }
        .small { font-size: 9pt; line-height: 1.18; }
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
