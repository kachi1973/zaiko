using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO;

using Npgsql;
using iTextSharp.text;
using iTextSharp.text.pdf;

/*
new iTextSharp.text.Rectangle(llx, lly, urx, ury);
new iTextSharp.text.Rectangle(左下x, 左下y, 右上x, 右上y);

+y
A
|          TOP
|     +------------B
|     |            |
|    L|            |R
|     |            |
|     A------------+
|         BOTTOM
0-------------------------> +x
A = (llx, lly)  B = (urx, ury)
(lower left X, lower left Y) - (upper right X, upper right Y)
単位はポイント pt です。1pt ≒ 0.352777mm 参考
A4 の場合、約 595x842 pt ⇔ 210x297 mm
*/

namespace zaikocgi
{
    class ShukkoPrint : PrintBase
    {
        private class Zaiko
        {
            public int? id { get; set; }
            public string basho { get; set; }
            public string basho_no { get; set; }
            public string basho_tana { get; set; }
            public string hinmoku_id { get; set; }
            public int? tanka { get; set; }
            public string kakaku { get; set; }
            public int? shuko { get; set; }
            public int? nyuko { get; set; }
            public string model_v { get; set; }
            public string dengen_in { get; set; }
            public string dengen_out { get; set; }
            public string biko { get; set; }
            public string seizo_date { get; set; }
            public int? zaiko_tekisei { get; set; }
            public int? zaiko_tuki { get; set; }
            public int? zaiko_new { get; set; }
            public int? zaiko_suu { get; set; }
            public int? zaiko_zan { get; set; }
            public DateTime? updated_at { get; set; }
            public DateTime? created_at { get; set; }
            public int? scaw_flg { get; set; }
            public int? jigyosyo_id { get; set; }
            public string scaw_fzcd { get; set; }
            public int? scaw_fzsu { get; set; }
            public string scaw_fhokancd { get; set; }
            public string scaw_flg_str { get; set; }
            public string scaw_flg_str_s { get; set; }
            public string jigyosyo_name { get; set; }
            public Lib.Lib.Hinmoku hinmoku { get; set; }
            public int? kashi_suu { get; set; }
            public int? sinsei_suu { get; set; }
            public string model_kind { get; set; }
        }
        private class Item
        {
            public int? id { get; set; }
            public int? shukko_id { get; set; }
            public int? zaiko_id { get; set; }
            public string biko { get; set; }
            public int? status { get; set; }
            public string seiban { get; set; }
			public int? req_suu { get; set; }
			public int? out_suu { get; set; }
            public int? used_suu { get; set; }
            public Zaiko zaiko { get; set; }
        }
        private class Data
        {
            public int? id { get; set; }
            public string user_id { get; set; }
            public DateTime? updated_at { get; set; }
            public DateTime? created_at { get; set; }
            public DateTime? shukko_date { get; set; }
            public int? status { get; set; }
            public string seiban { get; set; }
            public DateTime? status10_date { get; set; }
            public DateTime? status20_date { get; set; }
            public DateTime? status30_date { get; set; }
            public DateTime? status40_date { get; set; }
            public DateTime? status50_date { get; set; }
            public DateTime? status60_date { get; set; }
            public DateTime? status70_date { get; set; }
            public string shukko_user_id { get; set; }
            public string status10_user_id { get; set; }
            public string status20_user_id { get; set; }
            public string status30_user_id { get; set; }
            public string status40_user_id { get; set; }
            public string status50_user_id { get; set; }
            public string status60_user_id { get; set; }
            public string status70_user_id { get; set; }
            public string updated_user_id { get; set; }
            public int? jigyosyo_id { get; set; }
            public string status_str { get; set; }
            public string user_name { get; set; }
            public string bumon_name { get; set; }
            public string jigyosyo_name { get; set; }
            public string status10_user_name { get; set; }
            public string status20_user_name { get; set; }
            public string status30_user_name { get; set; }
            public string status40_user_name { get; set; }
            public string status50_user_name { get; set; }
            public string status60_user_name { get; set; }
            public string status70_user_name { get; set; }
            public Item[] items { get; set; }
        }
        public void run()
        {
            const int PAGE_LINE_MAX = 12;
            var r = Console.OpenStandardInput();
            var buf = new byte[1024 * 1024 * 3];
            int buf_size = 0;
            while (true)
            {
                var len = r.Read(buf, buf_size, buf.Length - buf_size);
                if (len == 0) break;
                buf_size += len;
            }
            Array.Resize(ref buf, buf_size);
            var json_str = System.Text.Encoding.UTF8.GetString(buf);
            var data = Program.GetJson<Data>(json_str);
            var bf = BaseFont.CreateFont(Path.Combine(Environment.SystemDirectory.Replace("system32", "fonts"), "msmincho.ttc,1"), BaseFont.IDENTITY_H, BaseFont.EMBEDDED);
            fntL = new Font(bf, 15f);
            fntM = new Font(bf, 8.5f);
            //FontFactory.RegisterDirectory(Environment.SystemDirectory.Replace("system32", "fonts"));
            //fnt = FontFactory.GetFont("ＭＳ Ｐゴシック", 8.5f, Font.NORMAL);
            using (var st = Console.OpenStandardOutput())
            {
                using (var doc = new Document(PageSize.A4.Rotate()))
                {
                    using (var writer = PdfWriter.GetInstance(doc, st))
                    {
                        doc.Open();
                        var cb = writer.DirectContent;
                        var zaiko0 = data.items.Where(z => z.zaiko.scaw_flg == 0).ToArray();
                        var zaiko1 = data.items.Where(z => z.zaiko.scaw_flg == 1).ToArray();
                        var zaiko_max = Math.Max(zaiko0.Count(), zaiko1.Count());
                        var page_max = Math.Ceiling((double)zaiko_max / PAGE_LINE_MAX);
                        for (var page_idx = 0; page_idx < page_max; ++page_idx)
                        {
                            var base_rct = new Rectangle(doc.PageSize);
                            float row_h = 16;
							//public const int ALIGN_LEFT = 0;
							//public const int ALIGN_CENTER = 1;
							//public const int ALIGN_RIGHT = 2;
							//                 品, 型 , V,  種, 名, メ, 備, 数, 数, 場, 場, 棚, 数, 数
							float[] cols_w = { 60, 100, 40, 60, 80, 70, 70, 35, 35, 50, 70, 40, 35, 35 };
                            int[] cols_a_h = { 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1 };
                            int[] cols_a_d = { 0, 0, 0, 0, 0, 0, 0, 2, 2, 0, 0, 0, 2, 2 };
                            base_rct.Left += doc.LeftMargin;
                            base_rct.Right -= doc.RightMargin;
                            base_rct.Top -= doc.TopMargin;
                            base_rct.Bottom += doc.BottomMargin;
                            var rct = new Rectangle(base_rct);
                            // ヘッダ１
                            rct.Bottom = base_rct.Top - 20f;
                            set_object(cb, rct, "出庫依頼", Element.ALIGN_CENTER, this.fntL, false);
                            // ヘッダ２
                            rct.NextRow();
                            rct.Bottom = rct.Top - row_h;
                            set_object(cb, rct, $"[出庫ID:{data.id}] [事業所:{data.jigyosyo_name,-10}] [依頼者:{data.user_name,-10}] [在庫担当:{data.status20_user_name,-10}] [受注番号:{data.seiban,-10}] [出庫希望日:{date_str(data.shukko_date)}] [出庫日:{date_str(data.status20_date)}] [状態:{data.status_str}]", Element.ALIGN_LEFT, this.fntM, false);
                            set_object(cb, rct, $"出力日:{DateTime.Now:yyyy/MM/dd HH:mm} ({page_idx + 1}/{page_max})", Element.ALIGN_RIGHT, this.fntM, false);
                            // テーブル
                            var tables = new[] {
                                new {
                                    scaw_flg = 1,
                                    name = "ＳＣＡＷ品",
                                },
                                new {
                                    scaw_flg = 0,
                                    name = "貯蔵品",
                                },
                            };
							foreach (var table in tables)
							{
								// ヘッダ１
								rct.NextRow();
								rct.Left = base_rct.Left;
								rct.SetWidth(cols_w[0] + cols_w[1] + cols_w[2] + cols_w[3] + cols_w[4] + cols_w[5] + cols_w[6] + cols_w[7] + cols_w[8]);
								set_object(cb, rct, table.name, Element.ALIGN_CENTER, this.fntM, true);
								rct.NextCol();
								rct.SetWidth(cols_w[9] + cols_w[10] + cols_w[11]);
								set_object(cb, rct, "保管場所", Element.ALIGN_CENTER, this.fntM, true);
								rct.NextCol();
								rct.SetWidth(cols_w[12] + cols_w[13]);
								set_object(cb, rct, "担当者", Element.ALIGN_CENTER, this.fntM, true);
								// ヘッダ２
								Action<Object[], int[]> f = (prms, cols_a) =>
								{
									rct.NextRow();
									rct.Left = base_rct.Left;
									for (var c_idx = 0; c_idx < cols_w.Length; ++c_idx)
									{
										rct.SetWidth(cols_w[c_idx]);
										set_object(cb, rct, prms[c_idx], cols_a[c_idx], this.fntM, true);
										rct.NextCol();
									}
								};
                                var sinsei = data.status < 20;
								f(new object[]
                                {
                                    "品目コード",
                                    "型式",
                                    "Ver",
                                    "種別",
                                    "名称",
                                    "メーカー",
                                    "部品備考",
                                    "在庫数",
                                    "貸出中",
                                    "場所名",
                                    "場所No.",
                                    "棚No.",
									sinsei ? "申請数" : "出庫数",
                                    "使用数",
                                }, cols_a_h);
								var zaiko = table.scaw_flg == 0 ? zaiko0 : zaiko1;
								for (var row_cnt = 0; row_cnt < PAGE_LINE_MAX; ++row_cnt)
								{
									var row_idx = (PAGE_LINE_MAX * page_idx) + row_cnt;
									if (row_idx < zaiko.Length)
									{
										var item = zaiko[row_idx];
										f(new object[]
										{
											item.zaiko.hinmoku_id,
											item.zaiko.hinmoku.model,
											item.zaiko.model_v,
											item.zaiko.model_kind,
											item.zaiko.hinmoku.name,
											item.zaiko.hinmoku.maker,
											item.zaiko.biko,
											item.zaiko.zaiko_suu,
											item.zaiko.kashi_suu,
											item.zaiko.basho,
											item.zaiko.basho_no,
											item.zaiko.basho_tana,
											sinsei ? item.req_suu : item.out_suu,
											item.used_suu,
										}, cols_a_d);
									}
									else
									{
										f(new object[] { "", "", "", "", "", "", "", "", "", "", "", "", "", "", }, cols_a_d);
									}
								}
								rct.NextRow();
							}
                            doc.NewPage();
                        }
                        doc.Close();
                    }
                }
                st.Close();
            }
        }
    }
}
