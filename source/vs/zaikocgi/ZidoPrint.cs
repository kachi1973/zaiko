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
    class ZidoPrint : PrintBase
    {
        private const float row_height_def = 18f;
        public class Item
        {
            public int? id { get; set; }
            public int? zido_id { get; set; }
            public string hinmoku_id { get; set; }
            public string seiban { get; set; }
            public int? suu { get; set; }
            public DateTime? updated_at { get; set; }
            public DateTime? created_at { get; set; }
            public int? zaiko_id { get; set; }
            public string name { get; set; }
            public string model { get; set; }
            public int? to_jigyosyo_id { get; set; }
            public string to_basho { get; set; }
            public string to_basho_no { get; set; }
            public string to_basho_tana { get; set; }
            public string kubun { get; set; }
            public Lib.Lib.Hinmoku hinmoku { get; set; }
            public Lib.Lib.Zaiko zaiko { get; set; }
            public string from_str { get; set; }
            public string to_str { get; set; }
            public int? to_zaiko_id { get; set; }
            public Lib.Lib.Zaiko to_zaiko { get; set; }
        }
        public class Root
        {
            public int? id { get; set; }
            public DateTime? inout_date { get; set; }
            public string souko { get; set; }
            public string sizai { get; set; }
            public string juchu_id { get; set; }
            public string bumon_id { get; set; }
            public string in_riyuu { get; set; }
            public string out_riyuu { get; set; }
            public string kubun { get; set; }
            public string tana { get; set; }
            public string biko { get; set; }
            public int? status { get; set; }
            public DateTime? status10_date { get; set; }
            public string status10_user_id { get; set; }
            public DateTime? status20_date { get; set; }
            public string status20_user_id { get; set; }
            public DateTime? status30_date { get; set; }
            public string status30_user_id { get; set; }
            public DateTime? status40_date { get; set; }
            public string status40_user_id { get; set; }
            public DateTime? status50_date { get; set; }
            public string status50_user_id { get; set; }
            public DateTime? status60_date { get; set; }
            public string status60_user_id { get; set; }
            public DateTime? status70_date { get; set; }
            public string status70_user_id { get; set; }
            public DateTime? updated_at { get; set; }
            public DateTime? created_at { get; set; }
            public int? from_type { get; set; }
            public int? from_jigyosyo_id { get; set; }
            public string from_basho { get; set; }
            public string from_basho_no { get; set; }
            public string from_basho_tana { get; set; }
            public int? to_type { get; set; }
            public int? to_jigyosyo_id { get; set; }
            public string to_basho { get; set; }
            public string to_basho_no { get; set; }
            public string to_basho_tana { get; set; }
            public int? parent_kind { get; set; }
            public int? parent_id { get; set; }
            public string from_biko { get; set; }
            public string to_biko { get; set; }
            public string bumon_name { get; set; }
            public string status00_user_name { get; set; }
            public string status10_user_name { get; set; }
            public string status20_user_name { get; set; }
            public string status30_user_name { get; set; }
            public string status40_user_name { get; set; }
            public string status50_user_name { get; set; }
            public string status60_user_name { get; set; }
            public string status70_user_name { get; set; }
            public string parent_str { get; set; }
            public string parent_url { get; set; }
            public string from_str { get; set; }
            public string to_str { get; set; }
            public List<Item> items { get; set; }
        }
        public void run()
        {
            const int PAGE_LINE_MAX = 5;
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
            var data = Program.GetJson<Root>(json_str);
            using (var st = Console.OpenStandardOutput())
            {
                using (var doc = new Document(PageSize.A4, 0f, 0f, 40f, 0f))
                {
                    using (var writer = PdfWriter.GetInstance(doc, st))
                    {
                        doc.Open();
                        var cb = writer.DirectContent;
                        var base_rct = new Rectangle(doc.PageSize);
                        base_rct.Left += doc.LeftMargin;
                        base_rct.Right -= doc.RightMargin;
                        base_rct.Top -= doc.TopMargin;
                        base_rct.Bottom += doc.BottomMargin;
                        var page_max = Math.Ceiling((double)data.items.Count / PAGE_LINE_MAX);
                        for (var page_idx = 0; page_idx < page_max; ++page_idx)
                        {

                            var rct = new Rectangle(base_rct);
                            var table = new PdfPTable(26);
                            //
                            this.row_height = row_height_def * 2;
                            var c = get_cell("", false, 3, 1, 1, this.fntL);
                            c.Border = 0;
                            table.AddCell(c);
                            c = get_cell("倉庫移動伝票", false, 20, 1, 1, this.fntL);
                            c.Border = 0;
                            table.AddCell(c);
                            c = get_cell($"{page_idx + 1}/{page_max}", false, 3, 1, 2, this.fntM);
                            c.Border = 0;
                            table.AddCell(c);
                            //
                            this.row_height = row_height_def;
                            table.AddCell(get_cell("発行No.", true, 3));
                            table.AddCell(get_cell(data.id, false, 5));
                            table.AddCell(get_cell("発行日", true, 3));
                            table.AddCell(get_cell(this.get_date(data.status10_date), false, 6));
                            table.AddCell(get_cell("区分", true, 3));
                            table.AddCell(get_cell("棚移動", false, 6));
                            //
                            this.row_height = row_height_def * 2;
                            table.AddCell(get_cell("依頼\n担当者", true, 3));
                            table.AddCell(get_cell(data.status10_user_id + "\n" + data.status10_user_name, false, 5));
                            table.AddCell(get_cell("入庫\n出庫日", true, 3));
                            table.AddCell(get_cell(this.get_date(data.inout_date), false, 6));
                            table.AddCell(get_cell("資材\n区分", true, 3));
                            table.AddCell(get_cell(data.sizai, false, 6));
                            //
                            table.AddCell(get_cell("受注\nNo.", true, 3));
                            table.AddCell(get_cell(data.juchu_id, false, 5));
                            table.AddCell(get_cell("責任部署", true, 3));
                            table.AddCell(get_cell(data.bumon_name, false, 17));
                            //
                            table.AddCell(get_cell("入庫理由", true, 3));
                            table.AddCell(get_cell(data.in_riyuu, false, 10));
                            table.AddCell(get_cell("出庫理由", true, 3));
                            table.AddCell(get_cell(data.out_riyuu, false, 10));
                            //
                            table.AddCell(get_cell("移動元\n倉庫", true, 3));
                            table.AddCell(get_cell(data.from_str, false, 10));
                            //
                            table.AddCell(get_cell("移動先\n倉庫", true, 3));
                            table.AddCell(get_cell(data.to_str, false, 10));
                            //
                            this.row_height = row_height_def;
                            for (var row_idx = 0; row_idx < PAGE_LINE_MAX; ++row_idx)
                            {
                                var row_idx2 = row_idx + (PAGE_LINE_MAX * page_idx);
                                table.AddCell(get_cell(row_idx2 + 1, true, 1, 3, 1, this.fntM));
                                if (row_idx2 < data.items.Count)
                                {
                                    var item = data.items[row_idx2];
                                    // １行目
                                    table.AddCell(get_cell("製番", true, 2));
                                    table.AddCell(get_cell(item.seiban, false, 5));
                                    table.AddCell(get_cell("品名", true, 2));
                                    table.AddCell(get_cell(item.hinmoku != null ? item.hinmoku.name : "", false, 7, 0));
                                    table.AddCell(get_cell("在庫区分", true, 3));
                                    table.AddCell(get_cell(item.kubun, false, 6));
                                    // ２行目
                                    table.AddCell(get_cell("品番", true, 2));
                                    table.AddCell(get_cell(item.hinmoku_id, false, 5));
                                    table.AddCell(get_cell("型式", true, 2));
                                    table.AddCell(get_cell(item.hinmoku != null ? item.hinmoku.model : "", false, 7, 0));
                                    table.AddCell(get_cell("数量", true, 3));
                                    table.AddCell(get_cell(item.suu, false, 6, 1, 2, this.fntM, "個"));
                                    // ３行目
                                    table.AddCell(get_cell("移動元", true, 2));
									table.AddCell(get_cell(item.from_str, false, 10, 1, 0, this.fntS));
                                    table.AddCell(get_cell("移動先", true, 2));
									table.AddCell(get_cell(item.to_str, false, 11, 1, 0, this.fntS));

                                }
                                else
                                {
                                    // １行目
                                    table.AddCell(get_cell("製番", true, 2));
                                    table.AddCell(get_cell("", false, 5));
                                    table.AddCell(get_cell("品名", true, 2));
                                    table.AddCell(get_cell("", false, 7, 1));
                                    table.AddCell(get_cell("在庫区分", true, 3));
                                    table.AddCell(get_cell("", false, 6));
                                    // ２行目
                                    table.AddCell(get_cell("品番", true, 2));
                                    table.AddCell(get_cell("", false, 5));
                                    table.AddCell(get_cell("型式", true, 2));
                                    table.AddCell(get_cell("", false, 7, 1));
                                    table.AddCell(get_cell("数量", true, 3));
                                    table.AddCell(get_cell("", false, 6, 1));
                                    // ３行目
                                    table.AddCell(get_cell("移動元", true, 2));
                                    table.AddCell(get_cell("", false, 10));
                                    table.AddCell(get_cell("移動先", true, 2));
                                    table.AddCell(get_cell("", false, 11));
                                }
                            }
                            //
                            this.row_height = 50f;
                            table.AddCell(get_cell("備考", true, 3));
                            c = get_cell(data.biko, false, 26, 0);
                            c.VerticalAlignment = Element.ALIGN_TOP;
                            table.AddCell(c);
                            //
                            this.row_height = row_height_def;
                            table.AddCell(get_cell("", false, 6, 2, 1, this.fntM));
                            table.AddCell(get_cell("承認", true, 4));
                            table.AddCell(get_cell("投入担当", true, 4));
                            table.AddCell(get_cell("出庫担当", true, 4));
                            table.AddCell(get_cell("所属長", true, 4));
                            table.AddCell(get_cell("依頼者", true, 4));
                            //
                            this.row_height = 70f;
                            table.AddCell(get_cell("", false, 4));
                            table.AddCell(get_cell("", false, 4));
                            if (data.status30_user_name != null)
                            {
                                table.AddCell(get_cell(Hanko.Image.Hanko(data.status30_user_name, get_date(data.status30_date), Hanko.Program.Names, 128), false, 4, 1));
                            }
                            else
                            {
                                table.AddCell(get_cell("", false, 4));
                            }
                            if (data.status20_user_name != null)
                            {
                                table.AddCell(get_cell(Hanko.Image.Hanko(data.status20_user_name, get_date(data.status20_date), Hanko.Program.Names, 128), false, 4, 1));
                            }
                            else
                            {
                                table.AddCell(get_cell("", false, 4));
                            }
                            if (data.status10_user_name != null)
                            {
                                table.AddCell(get_cell(Hanko.Image.Hanko(data.status10_user_name, get_date(data.status10_date), Hanko.Program.Names, 128), false, 4, 1));
                            }
                            else
                            {
                                table.AddCell(get_cell("", false, 4));
                            }
                            //
                            this.row_height = row_height_def * 1.5f;
                            c = get_cell("※使用した場合下記に必要事項を記入のうえ，投入依頼すること。使用せず，美和へ返却する場合倉庫戻しすること。", false, 26, 1, 0, fntM_red);
                            c.BorderWidth = 0f;
                            table.AddCell(c);
                            //
                            c = get_cell("投入・倉庫移動（戻し）", false, 10, 1, 0, fntL);
                            c.BorderWidth = 0f;
                            table.AddCell(c);
                            table.AddCell(get_cell("担当部署", true, 3, 1, 1, fntM));
                            table.AddCell(get_cell("", false, 5, 1, 0, fntM));
                            table.AddCell(get_cell("担当者", true, 3, 1, 1, fntM));
                            table.AddCell(get_cell("", false, 5, 1, 0, fntM));
                            //
                            c = get_cell("", false, 26, 1, 0, fntM);
                            c.BorderWidth = 0f;
                            table.AddCell(c);
                            //
                            table.AddCell(get_cell("内\n容", true, 1, 2, 1, fntM));
                            table.AddCell(get_cell("投入", true, 2, 1, 1, fntM));
                            table.AddCell(get_cell("投入製番", true, 3, 1, 1, fntM));
                            table.AddCell(get_cell("", false, 5, 1, 0, fntM));
                            table.AddCell(get_cell("数量", true, 2, 1, 1, fntM));
                            table.AddCell(get_cell("", false, 5, 1, 0, fntM));
                            table.AddCell(get_cell("所属長", true, 4, 1, 1, fntM));
                            table.AddCell(get_cell("担当者", true, 4, 1, 1, fntM));
                            //
                            table.AddCell(get_cell("移動", true, 2, 1, 1, fntM));
                            table.AddCell(get_cell("品名", true, 3, 1, 1, fntM));
                            table.AddCell(get_cell("", false, 12, 1, 0, fntM));
                            table.AddCell(get_cell("", false, 4, 2, 0, fntM));
                            table.AddCell(get_cell("", false, 4, 2, 0, fntM));
                            //
                            c = get_cell("", false, 3, 1, 0, fntM);
                            c.BorderWidth = 0f;
                            table.AddCell(c);
                            table.AddCell(get_cell("投入場所", true, 3, 1, 1, fntM));
                            table.AddCell(get_cell("", false, 12, 1, 0, fntM));
                            doc.Add(table);
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
