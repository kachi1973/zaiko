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
	class ZaikoExport
	{
		private const String WSNAME = "在庫マスタ";
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
			/*
			using (var wb = new XLWorkbook(@"C:\Users\kachi\Downloads\zaikolist - コピー.xlsx"))
			{
				var ws = wb.Worksheet(1);
				var cell = ws.Cell(2, 7);
				var s = cell.Style;
			}
			*/
			var zaiko = Properties.Settings.Default.schemaname;
			using (var conn = new NpgsqlConnection(Properties.Settings.Default.connect))
			{
				conn.Open();
				using (var cmd = new NpgsqlCommand(
					"select " +
					$"{zaiko}.zaikos.*, " +
					"public.jigyosyos.name jigyosyos_name, " +
					"hinmokus.model hinmokus_model, " +
					"hinmokus.name hinmokus_name, " +
					"hinmokus.maker hinmokus_maker " +
					$"from {zaiko}.zaikos " +
					$"left join public.jigyosyos ON (public.jigyosyos.id = {zaiko}.zaikos.jigyosyo_id) " +
					$"left join {zaiko}.hinmokus ON ({zaiko}.hinmokus.id = {zaiko}.zaikos.hinmoku_id) " +
					"order by id", conn))
				{
					/*
					NpgsqlParameter prm;
					prm = cmd.Parameters.Add(new NpgsqlParameter("parent_id", NpgsqlTypes.NpgsqlDbType.Integer));
					prm.Value = 123;
					*/
					var row = 1;
					var rdr = cmd.ExecuteReader();
					using (var wb = new XLWorkbook())
					{
						var ws = wb.AddWorksheet(ZaikoExport.WSNAME);
						var col = 1;
						this.SetCell(ws.Cell(row, col++), "ID", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "在庫区分", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "事業所", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "場所名", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "場所No.", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "棚No.", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "貯蔵品コード", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "販売価格", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "型式", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "Ver", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "種別", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "備考", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "名称", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "メーカ", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "製造年月", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "在庫数", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "単価", XLDataType.Text, true);
						this.SetCell(ws.Cell(row, col++), "適正在庫", XLDataType.Text, true);
						row++;
						while (rdr.Read())
						{
							col = 1;
							this.SetCell(ws.Cell(row, col++), rdr["id"], XLDataType.Number);
							this.SetCell(ws.Cell(row, col++), (int?)rdr["scaw_flg"] == 1 ? "SCAW品" : "貯蔵品", XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["jigyosyos_name"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["basho"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["basho_no"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["basho_tana"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["hinmoku_id"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["kakaku"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["hinmokus_model"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["model_v"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["model_kind"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["biko"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["hinmokus_name"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["hinmokus_maker"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["seizo_date"], XLDataType.Text);
							this.SetCell(ws.Cell(row, col++), rdr["zaiko_suu"], (XLDataType)1000);
							this.SetCell(ws.Cell(row, col++), rdr["tanka"], (XLDataType)1000, false, XLColor.FromArgb(255, 255, 200));
							this.SetCell(ws.Cell(row, col++), rdr["zaiko_tekisei"], (XLDataType)1000, false, XLColor.FromArgb(255, 255, 200));
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
		public void run2(string filepath)
		{
			var zaiko = Properties.Settings.Default.schemaname;
			var change_rows = 0;
			using (var st = new FileStream(filepath, FileMode.Open))
			{
				using (var wb = new XLWorkbook(st))
				{
					var ws = wb.Worksheet(ZaikoExport.WSNAME);
					using (var conn = new NpgsqlConnection(Properties.Settings.Default.connect))
					{
						conn.Open();
						using (var tran = conn.BeginTransaction())
						{
							for (var row = 2; row < 10000; row++)
							{
								// ID
								var id = get_int(ws.Cell(row, 1).Value);
								if (!id.HasValue) break;
								// 単価
								var tanka = get_int(ws.Cell(row, 17).Value);
								// 適正価格
								var tekisei = get_int(ws.Cell(row, 18).Value);
								int? db_tanka = null;
								int? db_tekisei = null;
								using (var cmd = new NpgsqlCommand($"select * from {zaiko}.zaikos where id = :id", conn, tran))
								{
									NpgsqlParameter prm;
									prm = cmd.Parameters.Add(new NpgsqlParameter("id", NpgsqlTypes.NpgsqlDbType.Integer));
									prm.Value = id;
									var rdr = cmd.ExecuteReader();
									while (rdr.Read())
									{
										db_tanka = rdr["tanka"] as int?;
										db_tekisei = rdr["zaiko_tekisei"] as int?;
										break;
									}
									rdr.Close();
								}
								var change = false;
								if (db_tanka != tanka)
								{
									using (var cmd2 = new NpgsqlCommand($"update {zaiko}.zaikos set tanka = :tanka where id = :id", conn, tran))
									{
										NpgsqlParameter prm;
										prm = cmd2.Parameters.Add(new NpgsqlParameter("id", NpgsqlTypes.NpgsqlDbType.Integer));
										prm.Value = id;
										prm = cmd2.Parameters.Add(new NpgsqlParameter("tanka", NpgsqlTypes.NpgsqlDbType.Integer));
										if (tanka.HasValue)
										{
											prm.Value = tanka.Value;
										}
										else
										{
											prm.Value = DBNull.Value;
										}
										cmd2.ExecuteNonQuery();
										change=true;
									}
								}
								if (tekisei != db_tekisei)
								{
									using (var cmd2 = new NpgsqlCommand($"update {zaiko}.zaikos set zaiko_tekisei = :zaiko_tekisei where id = :id", conn, tran))
									{
										NpgsqlParameter prm;
										prm = cmd2.Parameters.Add(new NpgsqlParameter("id", NpgsqlTypes.NpgsqlDbType.Integer));
										prm.Value = id;
										prm = cmd2.Parameters.Add(new NpgsqlParameter("zaiko_tekisei", NpgsqlTypes.NpgsqlDbType.Integer));
										if(tekisei.HasValue)
										{
											prm.Value = tekisei.Value;
										}
										else
										{
											prm.Value = DBNull.Value;
										}
										cmd2.ExecuteNonQuery();
										change = true;
									}
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
