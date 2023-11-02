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
	class HinmokuExport
	{
		private const String WSNAME = "品目マスタ";
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
				cell.Value = value.ToString();
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
				using (var cmd = new NpgsqlCommand($"select * from {zaiko}.hinmokus order by id", conn))
				{
					var row = 1;
					var rdr = cmd.ExecuteReader();
					using (var wb = new XLWorkbook())
					{
						var ws = wb.AddWorksheet(HinmokuExport.WSNAME);
						var col = 1;
						this.SetCell(ws.Cell(row, col++), "品目コード", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "型式", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "名称", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "メーカ", XLDataType.Text, true);
						row++;
						while (rdr.Read())
						{
							col = 1;
							this.SetCell(ws.Cell(row, col++), rdr["id"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["model"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["name"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["maker"], XLDataType.Text);
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
		public string get_str(object val)
		{
			var str = val.ToString().Trim();
			if (str.Length == 0) str = null;
			return str;
		}
		public void run2(string filepath)
		{
			var zaiko = Properties.Settings.Default.schemaname;
			var change_rows = 0;
			using (var st = new FileStream(filepath, FileMode.Open))
			{
				using (var wb = new XLWorkbook(st))
				{
					var ws = wb.Worksheet(HinmokuExport.WSNAME);
					using (var conn = new NpgsqlConnection(Properties.Settings.Default.connect))
					{
						conn.Open();
						using (var tran = conn.BeginTransaction())
						{
							for (var row = 2; row < 10000; row++)
							{
								var change = false;
								// ID(品目コード)
								var id = get_str(ws.Cell(row, 1).Value);
								if (id == null) break;
								// 型式
								var model = get_str(ws.Cell(row, 2).Value);
								// 名称
								var name = get_str(ws.Cell(row, 3).Value);
								// メーカ
								var maker = get_str(ws.Cell(row, 4).Value);
								var type = 0;
								using (var cmd = new NpgsqlCommand($"select * from {zaiko}.hinmokus where id = :id", conn, tran))
								{
									NpgsqlParameter prm;
									prm = cmd.Parameters.Add(new NpgsqlParameter("id", NpgsqlTypes.NpgsqlDbType.Text));
									prm.Value = id;
									var rdr = cmd.ExecuteReader();
									while (rdr.Read())
									{
										var db_id = rdr["id"] as string;
										// 型式
										var db_model = rdr["model"] as string;
										// 名称
										var db_name = rdr["name"] as string;
										// メーカ
										var db_maker = rdr["maker"] as string;
										type = (model == db_model && name == db_name && maker == db_maker) ? 1 : 2;
										break;
									}
									rdr.Close();
								}
								switch (type)
								{
								case 0: // 新規
									using (var cmd = new NpgsqlCommand($"insert into {zaiko}.hinmokus (id, name, model, maker)values(:id, :name, :model, :maker)", conn, tran))
									{
										var prm = cmd.Parameters.Add(new NpgsqlParameter("id", NpgsqlTypes.NpgsqlDbType.Text));
										prm.Value = id;
										prm = cmd.Parameters.Add(new NpgsqlParameter("model", NpgsqlTypes.NpgsqlDbType.Text));
										prm.Value = model;
										prm = cmd.Parameters.Add(new NpgsqlParameter("name", NpgsqlTypes.NpgsqlDbType.Text));
										prm.Value = name;
										prm = cmd.Parameters.Add(new NpgsqlParameter("maker", NpgsqlTypes.NpgsqlDbType.Text));
										prm.Value = maker;
										cmd.ExecuteNonQuery();
									}
									change = true;
									break;
								case 1: // 変更なし
									break;
								case 2: // 更新
									using (var cmd = new NpgsqlCommand($"update {zaiko}.hinmokus set model = :model, name = :name, maker = :maker where id = :id", conn, tran))
									{
										var prm = cmd.Parameters.Add(new NpgsqlParameter("id", NpgsqlTypes.NpgsqlDbType.Text));
										prm.Value = id;
										prm = cmd.Parameters.Add(new NpgsqlParameter("model", NpgsqlTypes.NpgsqlDbType.Text));
										prm.Value = model;
										prm = cmd.Parameters.Add(new NpgsqlParameter("name", NpgsqlTypes.NpgsqlDbType.Text));
										prm.Value = name;
										prm = cmd.Parameters.Add(new NpgsqlParameter("maker", NpgsqlTypes.NpgsqlDbType.Text));
										prm.Value = maker;
										cmd.ExecuteNonQuery();
										change = true;
									}
									change = true;
									break;
								}
								if (change) ++change_rows;
							}
							tran.Commit();
						}
					}
				}
			}
			using (var st2 = new System.IO.MemoryStream())
			{
				string jsonString = JsonSerializer.Serialize(new
				{
					change_rows = change_rows,
				});
				var bytes = System.Text.Encoding.UTF8.GetBytes(jsonString);
				using (var std = Console.OpenStandardOutput())
				{
					std.Write(bytes, 0, bytes.Length);
				}
			}
		}
	}
}
