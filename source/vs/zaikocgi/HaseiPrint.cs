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
	class HaseiPrint : PrintBase
	{
		private const float row_height_def = 35f;
		public class Item
		{
			public int? id { get; set; }
			public int? hasei_id { get; set; }
			public int? suu { get; set; }
			public DateTime? updated_at { get; set; }
			public DateTime? created_at { get; set; }
			public int? zaiko_id { get; set; }
			public Lib.Lib.Zaiko zaiko { get; set; }
		}
		public class FileUrl
		{
			public string name { get; set; }
			public string path { get; set; }
		}
		public class Root
		{
			public int? id { get; set; }
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
			var data = Program.GetJson<Root>(json_str);
			this.cell_padding = 3;
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
							var table = new PdfPTable(9);
							table.WidthPercentage = 100;
							var c = get_cell("発生品入庫伝票", false, 9, 1, 1, this.fntL);
							c.Border = 0;
							table.AddCell(c);
							table.AddCell(get_cell("発行No.", true));
							table.AddCell(get_cell(data.id, false));
							table.AddCell(get_cell("入庫日", true));
							table.AddCell(get_cell(get_date(data.status10_date), false, 6));
							table.AddCell(get_cell("品目コード", true));
							table.AddCell(get_cell("品名", true, 2));
							table.AddCell(get_cell("型式(Ver,種別)", true, 2));
							table.AddCell(get_cell("数量", true));
							table.AddCell(get_cell("入庫先", true, 3));
							for (var row_idx = 0; row_idx < PAGE_LINE_MAX; ++row_idx)
							{
								var row_idx2 = row_idx + (PAGE_LINE_MAX * page_idx);
								if (row_idx2 < data.items.Count)
								{
									var item = data.items[row_idx2];
									table.AddCell(get_cell(item.zaiko.hinmoku_id));
									table.AddCell(get_cell(item.zaiko.hinmoku.name, false, 2));
									table.AddCell(get_cell($"{item.zaiko.hinmoku.model} {item.zaiko.model_v} {item.zaiko.model_kind}", false, 2));
									table.AddCell(get_cell(item.suu, false, 1, 2));
									table.AddCell(get_cell(item.zaiko.tana_str, false, 3));
								}
								else
								{
								}
							}
							table.AddCell(get_cell("備考", true, 9, 1));
							this.row_height = row_height_def * 12;
							c = get_cell(data.biko, false, 9);
							c.VerticalAlignment = Element.ALIGN_TOP;
							table.AddCell(c);
							this.row_height = row_height_def;
							table.AddCell(get_cell("", true, 3));
							table.AddCell(get_cell("所属長\n(部長)", true, 2));
							table.AddCell(get_cell("所属長\n(課長)", true, 2));
							table.AddCell(get_cell("担当者", true, 2));
							this.row_height = 80f;
							table.AddCell(get_cell("", false, 3));
							if (data.status70_user_name != null)
							{
								table.AddCell(get_cell(Hanko.Image.Hanko(data.status70_user_name, get_date(data.status70_date), Hanko.Program.Names, 128), false, 2, 1));
							}
							else
							{
								table.AddCell(get_cell("", false, 2));
							}
							if (data.status20_user_name != null)
							{
								table.AddCell(get_cell(Hanko.Image.Hanko(data.status20_user_name, get_date(data.status20_date), Hanko.Program.Names, 128), false, 2, 1));
							}
							else
							{
								table.AddCell(get_cell("", false, 2));
							}
							if (data.status10_user_name != null)
							{
								table.AddCell(get_cell(Hanko.Image.Hanko(data.status10_user_name, get_date(data.status10_date), Hanko.Program.Names, 128), false, 2, 1));
							}
							else
							{
								table.AddCell(get_cell("", false, 2));
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
