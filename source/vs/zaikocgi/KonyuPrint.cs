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
    class KonyuPrint : PrintBase
    {
        private const float row_height_def = 45f;
        public class Item
        {
            public int? id { get; set; }
            public int? konyu_id { get; set; }
            public string hinmoku_id { get; set; }
            public int? suu { get; set; }
            public int? tanka { get; set; }
            public DateTime? updated_at { get; set; }
            public DateTime? created_at { get; set; }
            public Lib.Lib.Hinmoku hinmoku { get; set; }
        }
        public class Kkamoku
        {
            public int? id { get; set; }
            public string code1 { get; set; }
            public string name1 { get; set; }
            public string code2 { get; set; }
            public string name2 { get; set; }
        }
        public class Konyu
        {
            public int? id { get; set; }
            public string seiban { get; set; }
            public DateTime? nouki_date { get; set; }
            public bool? mitumori { get; set; }
            public string biko { get; set; }
            public DateTime? hachu_date { get; set; }
            public string irai_no { get; set; }
            public string juchu_no { get; set; }
            public int? kkamoku_id { get; set; }
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
            public Kkamoku kkamoku { get; set; }
            public List<Item> items { get; set; }
        }
        public void run()
        {
            const int PAGE_LINE_MAX = 1;
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
            var data = Program.GetJson<Konyu>(json_str);
            using (var st = Console.OpenStandardOutput())
            {
                using (var doc = new Document(PageSize.A4, 20f, 20f, 40f, 10f))
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
                            var table = new PdfPTable(7);
                            var c = get_cell("", false, 1, 1, 1, this.fntL);
                            c.Border = 0;
                            table.AddCell(c);
                            c = get_cell("部　品　購　入　依　頼", false, 5, 1, 1, this.fntL);
                            c.Border = 0;
                            table.AddCell(c);
                            c = get_cell($"{page_idx + 1}/{page_max}", false, 1, 1, 2, this.fntM);
                            c.Border = 0;
                            table.AddCell(c);
                            table.AddCell(get_cell("担当者入力欄", true, 7, 1));
                            table.AddCell(get_cell("発行No.", true));
                            table.AddCell(get_cell(data.id, false, 6));
                            table.AddCell(get_cell("依頼日", true));
                            table.AddCell(get_cell(get_date(data.status10_date), false, 1));
                            table.AddCell(get_cell("依頼者", true));
                            table.AddCell(get_cell(data.status10_user_name, false, 1));
                            table.AddCell(get_cell("所属", true));
                            table.AddCell(get_cell(data.bumon_name, false, 2));
                            table.AddCell(get_cell("製番", true));
                            table.AddCell(get_cell(data.seiban, false, 1));
                            table.AddCell(get_cell("希望納期", true));
                            table.AddCell(get_cell(get_date(data.nouki_date), false, 1));
                            table.AddCell(get_cell("見積書", true));
                            table.AddCell(get_cell(data.mitumori.GetValueOrDefault(false) ? "有り" : "無し", false, 2));
                            table.AddCell(get_cell("品目コード", true));
                            table.AddCell(get_cell("品名", true, 2));
                            table.AddCell(get_cell("型式", true));
                            table.AddCell(get_cell("数量", true));
                            table.AddCell(get_cell(@"単価(\)", true));
                            table.AddCell(get_cell(@"金額(\)", true, 2));
                            for (var row_idx = 0; row_idx < PAGE_LINE_MAX; ++row_idx)
                            {
                                var row_idx2 = row_idx + (PAGE_LINE_MAX * page_idx);
                                if (row_idx2 < data.items.Count)
                                {
                                    var item = data.items[row_idx2];
                                    table.AddCell(get_cell(item.hinmoku_id));
                                    table.AddCell(get_cell(item.hinmoku != null ? item.hinmoku.name : "", false, 2));
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
                            table.AddCell(get_cell("備考", true, 7, 1));
                            this.row_height = row_height_def * 2;
                            c = get_cell(data.biko, false, 7);
                            c.VerticalAlignment = Element.ALIGN_TOP;
                            table.AddCell(c);
                            this.row_height = row_height_def;
                            table.AddCell(get_cell("管理者入力欄", true, 7, 1));
                            table.AddCell(get_cell("発注日", true));
                            table.AddCell(get_cell(get_date(data.hachu_date), false, 2));
                            table.AddCell(get_cell("購入依頼番号", true));
                            table.AddCell(get_cell(data.irai_no, false, 1));
                            table.AddCell(get_cell("注文番号", true));
                            table.AddCell(get_cell(data.juchu_no, false, 1));
                            table.AddCell(get_cell("勘定科目", true));
                            table.AddCell(get_cell(data.kkamoku != null ? $"{data.kkamoku.code1} - {data.kkamoku.name1} - {data.kkamoku.code2} - {data.kkamoku.name2}" : "", false, 6));
                            table.AddCell(get_cell("", false, 3));
                            table.AddCell(get_cell("部長承認", true));
                            table.AddCell(get_cell("課長承認", true));
                            table.AddCell(get_cell("受取日", true));
                            table.AddCell(get_cell("依頼者", true));
                            this.row_height = 80f;
                            table.AddCell(get_cell(" ", false, 3    ));
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
                            if (data.status40_user_name != null)
                            {
                                table.AddCell(get_cell(Hanko.Image.Hanko(data.status40_user_name, get_date(data.status40_date), Hanko.Program.Names, 128), false, 1, 1));
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
