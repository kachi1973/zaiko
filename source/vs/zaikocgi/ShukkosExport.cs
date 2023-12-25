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
	class ShukkosExport
    {
		private const String WSNAME = "部品出庫一覧";
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
            var recs = Program.GetJson<ShukkoPrint.Shukko[]>(json_str);
            var row = 1;
            using (var wb = new XLWorkbook())
			{
				var ws = wb.AddWorksheet(WSNAME);
				var col = 1;
				this.SetCell(ws.Cell(row, col++), "No.", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "事業所", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "受注番号", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "申請日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "出庫希望日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "出庫日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "返却日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "返却承認日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "製番投入日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "課長承認日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "完了日", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "所属", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "担当者", XLDataType.Text, true);
                this.SetCell(ws.Cell(row, col++), "状況", XLDataType.Text, true);
                row++;
                foreach(var rec in recs)
				{
					col = 1;
					this.SetCell(ws.Cell(row, col++), rec.id, XLDataType.Number);
                    this.SetCell(ws.Cell(row, col++), rec.jigyosyo_name, XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), rec.seiban, XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), Program.get_name(rec.status10_date, rec.status10_user_name), XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), Program.get_YMD(rec.shukko_date), XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), Program.get_name(rec.status20_date, rec.status20_user_name), XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), Program.get_name(rec.status30_date, rec.status30_user_name), XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), Program.get_name(rec.status40_date, rec.status40_user_name), XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), Program.get_name(rec.status50_date, rec.status50_user_name), XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), Program.get_name(rec.status60_date, rec.status60_user_name), XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), Program.get_name(rec.status70_date, rec.status70_user_name), XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), rec.bumon_name, XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), rec.user_name, XLDataType.Text);
                    this.SetCell(ws.Cell(row, col++), rec.status_str, XLDataType.Text);
                    row++;
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
