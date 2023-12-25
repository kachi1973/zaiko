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
    class JorenPrint : PrintBase
    {
        private const float row_height_def = 30f;
        public class Item
        {
            public int? id { get; set; }
            public int? joren_id { get; set; }
            public string hinmoku_id { get; set; }
            public int? suu { get; set; }
            public int? tanka { get; set; }
            public DateTime? updated_at { get; set; }
            public DateTime? created_at { get; set; }
            public Lib.Lib.Hinmoku hinmoku { get; set; }
        }
        public class FileUrl
        {
            public string name { get; set; }
            public string path { get; set; }
        }
        public class Joren
        {
            public int? id { get; set; }
            public string no { get; set; }
            public string bumon_id { get; set; }
            public string anken_id { get; set; }
            public string kubun { get; set; }
            public string anken { get; set; }
            public string kokyaku { get; set; }
            public DateTime? kouki_start { get; set; }
            public DateTime? kouki_end { get; set; }
            public DateTime? kojyo_end { get; set; }
            public string biko { get; set; }
            public string siyou { get; set; }
            public string kiki { get; set; }
            public int? status { get; set; }
            public string status_str { get; set; }
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
            public string bumon_name { get; set; }
            public string status00_user_name { get; set; }
            public string status10_user_name { get; set; }
            public string status20_user_name { get; set; }
            public string status30_user_name { get; set; }
            public string status40_user_name { get; set; }
            public string status50_user_name { get; set; }
            public string status60_user_name { get; set; }
            public string status70_user_name { get; set; }
            public List<Item> items { get; set; }
            public List<FileUrl> file_urls { get; set; }
        }
        public void run()
        {
            const int PAGE_LINE_MAX = 7;
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
            var data = Program.GetJson<Joren>(json_str);
            using (var st = Console.OpenStandardOutput())
            {
                using (var doc = new Document(PageSize.A4, 40f, 40f, 40f, 10f))
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
                            this.row_height = row_height_def;
                            var rct = new Rectangle(base_rct);
                            var table = new PdfPTable(6);
                            table.WidthPercentage = 100;
                            var c = get_cell($"No.{data.id}", false, 1, 1, 0, this.fntM);
                            c.Border = 0;
                            table.AddCell(c);
                            c = get_cell("情　報　連　絡　票", false, 4, 1, 1, this.fntL);
                            c.Border = 0;
                            table.AddCell(c);
                            c = get_cell($"{page_idx + 1}/{page_max}", false, 1, 1, 2, this.fntM);
                            c.Border = 0;
                            table.AddCell(c);
                            table.AddCell(get_cell("文章番号", true));
                            table.AddCell(get_cell(data.no, false, 2));
                            table.AddCell(get_cell("発行日", true));
                            table.AddCell(get_cell(get_date(data.status10_date), false, 2));
                            table.AddCell(get_cell("営業管理部門", true));
                            table.AddCell(get_cell(data.bumon_name, false, 5));
                            table.AddCell(get_cell("案件番号", true));
                            table.AddCell(get_cell(data.anken_id, false, 2));
                            table.AddCell(get_cell("区分・確度", true));
                            table.AddCell(get_cell(data.kubun, false, 2));
                            table.AddCell(get_cell("案件名", true));
                            table.AddCell(get_cell(data.anken, false, 5));
                            table.AddCell(get_cell("顧客名", true));
                            table.AddCell(get_cell(data.kokyaku, false, 5));
                            table.AddCell(get_cell("予定工期", true));
                            table.AddCell(get_cell($"自）{this.date_str(data.kouki_start)} ～ 至）{this.date_str(data.kouki_end)}", false, 5));
                            table.AddCell(get_cell("予定工場完", true));
                            table.AddCell(get_cell(this.get_date(data.kojyo_end), false, 5));
                            table.AddCell(get_cell("品目コード", true));
                            table.AddCell(get_cell("品名", true));
                            table.AddCell(get_cell("型式", true));
                            table.AddCell(get_cell("数量", true));
                            table.AddCell(get_cell(@"単価(\)", true));
                            table.AddCell(get_cell(@"金額(\)", true));
                            for (var row_idx = 0; row_idx < PAGE_LINE_MAX; ++row_idx)
                            {
                                var row_idx2 = row_idx + (PAGE_LINE_MAX * page_idx);
                                if (row_idx2 < data.items.Count)
                                {
                                    var item = data.items[row_idx2];
                                    table.AddCell(get_cell(item.hinmoku_id));
                                    table.AddCell(get_cell(item.hinmoku != null ? item.hinmoku.name : ""));
                                    table.AddCell(get_cell(item.hinmoku != null ? item.hinmoku.model : ""));
                                    table.AddCell(get_cell(item.suu, false, 1, 2));
                                    table.AddCell(get_cell(item.tanka, false, 1, 2));
                                    if (item.suu.HasValue && item.tanka.HasValue)
                                    {
                                        table.AddCell(get_cell((ulong?)item.suu * (ulong?)item.tanka, false, 1, 2));
                                    }
                                    else
                                    {
                                        table.AddCell(get_cell());
                                    }
                                }
                                else
                                {
                                    table.AddCell(get_cell(" "));
                                    table.AddCell(get_cell());
                                    table.AddCell(get_cell());
                                    table.AddCell(get_cell());
                                    table.AddCell(get_cell());
                                    table.AddCell(get_cell());
                                }
                            }
                            table.AddCell(get_cell("備考", true, 6, 1));
                            this.row_height = 80f;
                            c = get_cell(data.biko, false, 6);
                            c.VerticalAlignment = Element.ALIGN_TOP;
                            table.AddCell(c);
                            this.row_height = row_height_def;
                            table.AddCell(get_cell("仕様書添付", true));
                            table.AddCell(get_cell(0 < data.file_urls.Count ? "有り" : "無し", false, 2));
                            table.AddCell(get_cell("機器仕様", true));
                            table.AddCell(get_cell(data.kiki, false, 2));
                            table.AddCell(get_cell("", true));
                            table.AddCell(get_cell("", true));
                            table.AddCell(get_cell("", true));
                            table.AddCell(get_cell("所属長(部長)", true));
                            table.AddCell(get_cell("所属長(課長)", true));
                            table.AddCell(get_cell("担当者", true));
                            this.row_height = 80f;
                            table.AddCell(get_cell(" ", false));
                            table.AddCell(get_cell("", false));
                            table.AddCell(get_cell("", false));
                            if (data.status30_user_name != null)
                            {
                                table.AddCell(get_cell(Hanko.Image.Hanko(data.status30_user_name, get_date(data.status30_date), Hanko.Program.Names, 128), false, 1, 1));
                            }
                            else
                            {
                                table.AddCell(get_cell("", false));
                            }
                            if (data.status20_user_name != null)
                            {
                                table.AddCell(get_cell(Hanko.Image.Hanko(data.status20_user_name, get_date(data.status20_date), Hanko.Program.Names, 128), false, 1, 1));
                            }
                            else
                            {
                                table.AddCell(get_cell("", false));
                            }
                            if (data.status10_user_name != null)
                            {
                                table.AddCell(get_cell(Hanko.Image.Hanko(data.status10_user_name, get_date(data.status10_date), Hanko.Program.Names, 128), false, 1, 1));
                            }
                            else
                            {
                                table.AddCell(get_cell("", false));
                            }
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
