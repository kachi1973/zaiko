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
	class KZaikoExport
    {
		private const String WSNAME = "個別在庫マスタ";
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
			var zaiko = Properties.Settings.Default.schemaname;
			using (var conn = new NpgsqlConnection(Properties.Settings.Default.connect))
			{
				conn.Open();
				using (var cmd = new NpgsqlCommand(
					"select " +
					$"{zaiko}.kzaikos.*, " +
                    "hinmokus.model hinmokus_model, " +
                    "hinmokus.name hinmokus_name, " +
                    "hinmokus.maker hinmokus_maker " +
                    $"from {zaiko}.kzaikos " +
                    $"left join {zaiko}.zaikos ON ({zaiko}.zaikos.id = {zaiko}.kzaikos.zaiko_id) " +
                    $"left join {zaiko}.hinmokus ON ({zaiko}.hinmokus.id = {zaiko}.kzaikos.hinmoku_id) " +
					"order by id", conn))
				{
					var row = 1;
					var rdr = cmd.ExecuteReader();
					using (var wb = new XLWorkbook())
					{
						var ws = wb.AddWorksheet(KZaikoExport.WSNAME);
						var col = 1;
						this.SetCell(ws.Cell(row, col++), "ID", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "在庫ID", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "作成日", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "貯蔵品コード", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "型式", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "型式Ver", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "型式種別", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "備考", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "名称", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "メーカ", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "製造年月", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "在庫製番", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "状態", XLDataType.Text, true);
						row++;
						while (rdr.Read())
						{
							col = 1;
							this.SetCell(ws.Cell(row, col++), rdr["id"], XLDataType.Number);
                            this.SetCell(ws.Cell(row, col++), rdr["zaiko_id"], XLDataType.Number);
                            this.SetCell(ws.Cell(row, col++), Program.get_YMD(rdr["created_at"]), XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), rdr["hinmoku_id"], XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), rdr["hinmokus_model"], XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), rdr["model_v"], XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), rdr["model_kind"], XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), rdr["biko"], XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), rdr["hinmokus_name"], XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), rdr["hinmokus_maker"], XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), Program.get_YMD(rdr["seizo_date"]), XLDataType.Text);
                            this.SetCell(ws.Cell(row, col++), rdr["seiban"], XLDataType.Text);
                            var str = "保管中";
                            switch(rdr["status"])
                            {
                            case 1:
                                str = "出庫中";
                                break;
                            case 9999:
                                str = "使用済";
                                break;
                            }
                            this.SetCell(ws.Cell(row, col++), str, XLDataType.Text);
                            row++;
						}
						ws.RangeUsed().SetAutoFilter(true);
						ws.Columns().AdjustToContents((double)4, (double)16);
						using (var st = new System.IO.MemoryStream())
						{
							wb.SaveAs(st);
							using (var std = Console.OpenStandardOutput())
							{
								std.Write(st.GetBuffer(), 0, (int)st.Length);
							}
						}
					}
					rdr.Close();
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
