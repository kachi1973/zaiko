using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Text.Json;
using System.Text.Json.Serialization;
using System.IO;

using Npgsql;
using iTextSharp.text;
using iTextSharp.text.pdf;
using System.Diagnostics;
using ClosedXML.Excel;

namespace zaikocgi
{
	class ZidosExport
    {
		private const String WSNAME = "倉庫移動伝票一覧";
		public IXLCell SetCell(IXLCell cell, Object value, XLDataType type, bool header = false, XLColor clr = null)
		{
			//
			switch (type)
			{
				case XLDataType.Number:
					cell.DataType = type;
					cell.Style.NumberFormat.Format = "0";
					break;
				case (XLDataType)1000:
					cell.DataType = XLDataType.Number;
					cell.Style.NumberFormat.Format = "#,##0";
					break;
				case XLDataType.Text:
					cell.DataType = type;
					cell.Style.NumberFormat.Format = "@";
					break;
				default:
					cell.DataType = type;
					break;
			}
			//
			if (cell.Value is int)
			{
				cell.Value = (int)value;
			}
			else if (cell.Value is DBNull)
			{
				cell.Value = null;
			}
			else
			{
				cell.Value = value?.ToString();
			}
			//
			cell.Style.Border.SetOutsideBorder(XLBorderStyleValues.Thin);
			if (header)
			{
				cell.Style.Fill.BackgroundColor = XLColor.LightSkyBlue;
			}
			if (clr != null)
			{
				cell.Style.Fill.BackgroundColor = clr;
			}
			return cell;
		}
        public string get_name(DateTime? date, string name)
        {
            return $"{Program.get_YMD(date)} {name}";
        }
        public void run()
		{
            var r = Console.OpenStandardInput();
            var buf = new byte[1024 * 1024 * 30];
            int buf_size = 0;
            while (true)
            {
                var len = r.Read(buf, buf_size, buf.Length - buf_size);
                if (len == 0) break;
                buf_size += len;
            }
            Array.Resize(ref buf, buf_size);
            var json_str = System.Text.Encoding.UTF8.GetString(buf);
            var recs = Program.GetJson<ZidoPrint.Zido[]>(json_str);
            var row = 1;
            using (var wb = new XLWorkbook())
			{
				var ws = wb.AddWorksheet(WSNAME);
				var col = 1;
				this.SetCell(ws.Cell(row, col++), "No.", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "発行日(申請日)", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "入庫出庫日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "社員", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "責任部署", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "製番", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "品番", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "品名", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "型式", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "数量", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "出庫理由", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "入庫理由", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "備考", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "承認状況", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "関連", XLDataType.Text, true);
                row++;
                foreach (var rec in recs)
                {
                    foreach (var rr in rec.items)
                    {
                        col = 1;
                        this.SetCell(ws.Cell(row, col++), rec.id, XLDataType.Number);
                        this.SetCell(ws.Cell(row, col++), Program.get_YMD(rec.status10_date), XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), Program.get_YMD(rec.inout_date), XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), Program.get_name(rec.status10_date, rec.status10_user_name), XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rec.bumon_name, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rr.seiban, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rr.hinmoku_id, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rr.hinmoku?.name, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rr.hinmoku?.model, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rr.suu, XLDataType.Number);
                        this.SetCell(ws.Cell(row, col++), rec.out_riyuu, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rec.in_riyuu, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rec.biko, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rec.status_str, XLDataType.Text);
                        this.SetCell(ws.Cell(row, col++), rec.parent_str, XLDataType.Text);
                        row++;
                    }
                }
				ws.RangeUsed().SetAutoFilter(true);
				ws.Columns().AdjustToContents((double)4, (double)64);
				using (var st = new System.IO.MemoryStream())
				{
					wb.SaveAs(st);
					using (var std = Console.OpenStandardOutput())
					{
						std.Write(st.GetBuffer(), 0, (int)st.Length);
					}
				}
			}
		}
		public int? get_int(object val)
		{
			int num = 0;
			int? ret = null;
			if (int.TryParse(val.ToString(), out num)) ret = num;
			return ret;
		}
	}
}
