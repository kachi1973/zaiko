using Npgsql;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text.RegularExpressions;

namespace zaikocgi
{
	class Program
	{
		static void Main(string[] args)
		{
			switch (int.Parse(args[0]))
			{
			case 1: // 出庫印刷
				{
					var c = new ShukkoPrint();
					c.run();
				}
				break;
			case 2: // 情報連絡票印刷
				{
					var j = new JorenPrint();
					j.run();
				}
				break;
			case 3: // 倉庫移動伝票
				{
					var z = new ZidoPrint();
					z.run();
				}
				break;
			case 4: // 部品購入依頼
				{
					var k = new KonyuPrint();
					k.run();
				}
				break;
			case 5: // 在庫エクスポート
				{
					var ze = new ZaikoExport();
					ze.run();
				}
				break;
			case 6: // 在庫インポート
				{
					var ze = new ZaikoExport();
					ze.run2(args[1]);
				}
				break;
			case 7: // 発生品
				{
					var j = new HaseiPrint();
					j.run();
				}
				break;
			case 8: // 品目エクスポート
				{
					var he = new HinmokuExport();
					he.run();
				}
				break;
			case 9: // 品目エクスポート
				{
					var he = new HinmokuExport();
					he.run2(args[1]);
				}
				break;
			}
		}
		static public string get_YMD(object field)
		{
			var date = field as DateTime?;
			if (date.HasValue)
			{
				return date.Value.ToString("yyyy/MM/dd");
			}
			return null;
		}
		static public string get_cust_name(string connString, object _id, string name, string sep)
		{
			var id = _id as int?;
			if (id.HasValue)
			{
				using (var conn = new NpgsqlConnection(connString))
				{
					conn.Open();
					using (var cmd = new NpgsqlCommand($"select * from customers where id={id.Value}", conn))
					{
						var rdr = cmd.ExecuteReader();
						while (rdr.Read())
						{
							return get_cust_name(connString, rdr["parent_id"], (name != null ? $"{rdr["name"].ToString()}{sep}{name}" : rdr["name"].ToString()), sep);
						}
						rdr.Close();
					}
				}
			}
			return name;
		}
		static public string get_text(string connString, string sql)
		{
			using (var conn = new NpgsqlConnection(connString))
			{
				conn.Open();
				using (var cmd = new NpgsqlCommand(sql, conn))
				{
					var rdr = cmd.ExecuteReader();
					while (rdr.Read())
					{
						return rdr[0].ToString();
					}
					rdr.Close();
				}
			}
			return null;
		}
		static public string get_rosen_no1(object _rosen_no)
		{
			var rosen_no = _rosen_no as string;
			if (rosen_no != null)
			{
				if (get_rosen_no2(_rosen_no).HasValue)
				{
					return Regex.Replace(rosen_no, "[0-9０-９]+号.*", "").Trim();
				}
				else
				{
					return rosen_no;
				}
			}
			return String.Empty;
		}
		static public int? get_rosen_no2(object _rosen_no)
		{
			var rosen_no = _rosen_no as string;
			if (rosen_no != null)
			{
				var match = Regex.Match(rosen_no, "[0-9０-９]+号");
				if (match.Success) return int.Parse(match.Value.Replace("号", ""));
			}
			return null;
		}
		static public string get_umu(object _umu)
		{
			var umu = _umu as bool?;
			if (umu.HasValue && umu.Value)
			{
				return "有";
			}
			else
			{
				return "無";
			}
		}
		static public T GetJson<T>(string json_str)
		{
			var opt = new Newtonsoft.Json.JsonSerializerSettings();
			opt.DateTimeZoneHandling = Newtonsoft.Json.DateTimeZoneHandling.Local;
			return Newtonsoft.Json.JsonConvert.DeserializeObject<T>(json_str, opt);
		}
	}
}
