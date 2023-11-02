using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO;
using System.Text.RegularExpressions;
using Npgsql;
using OfficeOpenXml;
using System.Drawing;
using System.Drawing.Imaging;
using System.Diagnostics;
using Microsoft.CodeAnalysis.CSharp.Scripting;
using System.Reflection;

namespace xls2db2
{
    class Program
    {
        static void Main(string[] args)
        {
            ExcelPackage.LicenseContext = LicenseContext.NonCommercial;
            var str = File.ReadAllText(Assembly.GetEntryAssembly().Location + ".csx", Encoding.GetEncoding("shift_jis"));
            var opt = Microsoft.CodeAnalysis.Scripting.ScriptOptions.Default;
            var zaiko = new Zaiko();
            var task = CSharpScript.EvaluateAsync<int>(str, opt, zaiko);
            task.Wait();
        }
    }
    public class Record
    {
        public string file;
        public int jigyosyo_id;
        public string A;
        public string B;
        public string C;
        public string D;
        public string E;
        public string F;
        public decimal? H;
        public string I;
        public decimal? J;
        public decimal? K;
        public string L;
        public string M;
        public string N;
        public string O;
        public string P;
        public string Q;
        public string R;
        public string S;
        public string T;
        public decimal? U;
        public decimal? V;
        public decimal? W;
        public decimal? X;
        public decimal? Y;
        public string IJ;
        public string IK;
        public decimal? IL;
        public string IM;
        public string IN;
    }
    public class Zaiko
    {
        private List<Record> recs = new List<Record>();
        public string get_str(ExcelRange c)
        {
            if (c.Value == null)
            {
                return null;
            }
            return c.Value.ToString();
        }
        public decimal? get_dec(ExcelRange c)
        {
            if (c.Value == null)
            {
                return null;
            }
            var s = c.Value.ToString();
            decimal v = 0;
            if(decimal.TryParse(s, out v))
            {
                return v;
            }
            //Trace.WriteLine(s);
            return null;
        }
        public void Parse(string file, int jigyosyo_id, int type)
        {
            var inputExcelFile = new FileInfo(file);
            ExcelPackage inputFile = null;
            try
            {
                inputFile = new ExcelPackage(inputExcelFile);
            }
            catch (Exception e)
            {
                Lib.Lib.log(e.Message);
                return;
            }
            var rec_cnt = 0;
            var err_cnt = 0;
            foreach (var sheet in inputFile.Workbook.Worksheets)
            {
                if (sheet.Name == "使用部品一覧") continue;
                for (var y = 5; ; ++y)
                {
                    if (sheet.Cells["A" + y].Value != null && sheet.Cells["D" + y].Value != null)
                    {
                        err_cnt = 0;
                        var rec = new Record();
                        rec.file = file;
                        rec.jigyosyo_id = jigyosyo_id;
                        switch (type)
                        {
                        case 0:
                            rec.A = get_str(sheet.Cells["A" + y]);
                            rec.B = get_str(sheet.Cells["B" + y]);
                            rec.C = get_str(sheet.Cells["C" + y]);
                            rec.D = null;
                            rec.E = null;
                            rec.F = get_str(sheet.Cells["D" + y]);
                            rec.H = get_dec(sheet.Cells["F" + y]);
                            rec.I = get_str(sheet.Cells["G" + y]);
                            rec.J = get_dec(sheet.Cells["H" + y]);
                            rec.K = get_dec(sheet.Cells["I" + y]);
                            rec.L = get_str(sheet.Cells["J" + y]);
                            rec.M = get_str(sheet.Cells["K" + y]);
                            rec.N = get_str(sheet.Cells["L" + y]);
                            rec.O = get_str(sheet.Cells["M" + y]);
                            rec.P = get_str(sheet.Cells["N" + y]);
                            rec.Q = get_str(sheet.Cells["O" + y]);
                            rec.R = get_str(sheet.Cells["P" + y]);
                            rec.S = get_str(sheet.Cells["Q" + y]);
                            rec.T = get_str(sheet.Cells["R" + y]);
                            rec.U = get_dec(sheet.Cells["S" + y]);
                            rec.V = get_dec(sheet.Cells["T" + y]);
                            rec.W = get_dec(sheet.Cells["U" + y]);
                            rec.X = get_dec(sheet.Cells["V" + y]);
                            rec.Y = get_dec(sheet.Cells["W" + y]);
                            rec.IJ = get_str(sheet.Cells["IH" + y]);
                            rec.IK = get_str(sheet.Cells["II" + y]);
                            rec.IL = get_dec(sheet.Cells["IJ" + y]);
                            rec.IM = get_str(sheet.Cells["IK" + y]);
                            rec.IN = get_str(sheet.Cells["IL" + y]);
                            break;
                        default:
                            rec.A = get_str(sheet.Cells["A" + y]);
                            rec.B = get_str(sheet.Cells["B" + y]);
                            rec.C = get_str(sheet.Cells["C" + y]);
                            rec.D = get_str(sheet.Cells["D" + y]);
                            rec.E = get_str(sheet.Cells["E" + y]);
                            rec.F = get_str(sheet.Cells["F" + y]);
                            rec.H = get_dec(sheet.Cells["H" + y]);
                            rec.I = get_str(sheet.Cells["I" + y]);
                            rec.J = get_dec(sheet.Cells["J" + y]);
                            rec.K = get_dec(sheet.Cells["K" + y]);
                            rec.L = get_str(sheet.Cells["L" + y]);
                            rec.M = get_str(sheet.Cells["M" + y]);
                            rec.N = get_str(sheet.Cells["N" + y]);
                            rec.O = get_str(sheet.Cells["O" + y]);
                            rec.P = get_str(sheet.Cells["P" + y]);
                            rec.Q = get_str(sheet.Cells["Q" + y]);
                            rec.R = get_str(sheet.Cells["R" + y]);
                            rec.S = get_str(sheet.Cells["S" + y]);
                            rec.T = get_str(sheet.Cells["T" + y]);
                            rec.U = get_dec(sheet.Cells["U" + y]);
                            rec.V = get_dec(sheet.Cells["V" + y]);
                            rec.W = get_dec(sheet.Cells["W" + y]);
                            rec.X = get_dec(sheet.Cells["X" + y]);
                            rec.Y = get_dec(sheet.Cells["Y" + y]);
                            rec.IJ = get_str(sheet.Cells["IJ" + y]);
                            rec.IK = get_str(sheet.Cells["IK" + y]);
                            rec.IL = get_dec(sheet.Cells["IL" + y]);
                            rec.IM = get_str(sheet.Cells["IM" + y]);
                            rec.IN = get_str(sheet.Cells["IN" + y]);
                            break;
                        }
                        this.recs.Add(rec);
                        ++rec_cnt;
                        //Trace.WriteLine(inputExcelFile + ":A" + y + ":" + rec.F);
                    }
                    else
                    {
                        ++err_cnt;
                    }
                    if (10 < err_cnt)
                    {
                        break;
                    }
                }
                break;
            }
            if (rec_cnt == 0)
            {
                //Trace.WriteLine(inputExcelFile);
            }

        }
        public void Insert(string connString)
        {
            //Trace.WriteLine(connString);
            using (var conn = new NpgsqlConnection(connString))
            {
                var cnt = 0;
                var err = 0;
                conn.Open();
                foreach (var r in this.recs)
                {
                    using (var cmd = new NpgsqlCommand(@"select * from zaiko.zaikos where basho = :basho and basho_no = :basho_no and basho_tana = :basho_tana and hinmoku_id = :hinmoku_id and ((model_v = :model_v) or (model_v is null and :model_v is null)) and ((model_kind = :model_kind) or (model_kind is null and :model_kind is null)) and ((biko = :biko) or (biko is null and :biko is null))", conn))
                    {
                        NpgsqlParameter prm;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("basho", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.A;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("basho_no", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.B;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("basho_tana", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.C;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("hinmoku_id", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.F;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("model_v", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.M;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("model_kind", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.N;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("model_kind", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.N;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("biko", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.Q;
                        foreach (NpgsqlParameter p in cmd.Parameters)
                        {
                            if (p.Value == null) p.Value = DBNull.Value;
                        }
                        using (var reader = cmd.ExecuteReader())
                        {
                            if (reader.HasRows)
                            {
                                while (reader.Read())
                                {
                                    if(reader.GetDecimal(17) != r.X)
                                    {
                                        ++cnt;
                                        Trace.WriteLine($"ID:{reader[0]}, {reader.GetDecimal(17)} != {r.X}");
                                    }
                                }
                            }
                            else
                            {
                                ++err;
                                //Trace.WriteLine($"No hit:{r.A}");
                            }
                        }
                    }
                }
                Trace.WriteLine($"TOTAL:{cnt}");
                Trace.WriteLine($"ERROR:{err}");
            }
        }
    }
}
