#define _TOSYO

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

namespace xls2db
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
        public string sheet_name;
        public int sheet_row;
    }
    public class hinmoku
    {
        public string id;
        public string name;
        public string model;
        public string maker;
        public string sheet_name;
        public int sheet_row;
        public List<hinmoku> errs = new List<hinmoku>();
    }
    public class Zaiko
    {
        private List<Record> recs = new List<Record>();
        private Dictionary<string, hinmoku> hins = new Dictionary<string, hinmoku>();
        public string get_str(ExcelRange c)
        {
            if (c.Value == null)
            {
                return null;
            }
            return c.Value.ToString().Trim();
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
        public void Parse(string file, string sheetname, int jigyosyo_id)
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
            var sheet = inputFile.Workbook.Worksheets[sheetname];
            for (var y = 5; ; ++y)
            {
                if (sheet.Cells["A" + y].Value != null && sheet.Cells["F" + y].Value != null)
                {
                    err_cnt = 0;
                    var rec = new Record();
                    rec.sheet_name = Path.GetFileName(file);
                    rec.sheet_row = y;
                    rec.jigyosyo_id = jigyosyo_id;
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
                    if (rec.F != "削除予定")
                    {
                        this.recs.Add(rec);
                        ++rec_cnt;
                        //Trace.WriteLine(inputExcelFile + ":A" + y + ":" + rec.F);
                    }
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
            Trace.WriteLine($"{file}:{rec_cnt}");
        }
        public void Insert(string connString)
        {
            //Trace.WriteLine(connString);
            foreach (var r in this.recs)
            {
                var h = new hinmoku();
                h.id = r.F;
                h.name = r.R;
                h.model = r.L;
                h.maker = r.S;
                switch (h.name)
                {
                case "ﾌﾟﾘﾝﾄ基板":
                case "プリント基板":
                case "NCU":
                case "I/O基板":
                case "EK-62351":
                case "PA-456R":
                case "PA-457R":
                case "PA-458R":
                case "PA-501":
                case "EK-85633":
                case "EK-83443":
                case "EK-49980":
                case "EK-88064":
                case "EK-92739":
                case "EK-08244":
                case "EK-13039":
                case "PA-429":
                case "EK-34572":
                case "EK-93758":
                    h.name = "基板";
                    break;
                case "ﾓﾃﾞ基板":
                case "ﾃﾞﾑ基板":
                case "MODEM":
                case "ﾓﾃﾞﾑ":
                case "ﾓﾃﾞﾑﾕﾆｯﾄ":
                case "無線ﾓﾃﾞﾑ":
                    h.name = "ﾓﾃﾞﾑ基板";
                    break;
                case "LED":
                case "LEDユニット":
                case "LEDﾕﾆｯﾄ":
                case "LEDﾓｼﾞｭｰﾙ":
                    h.name = "LED表示器";
                    break;
                case "定電圧電源装置":
                case "ＤC/DCｺﾝﾊﾞｰﾀ":
                case "ｽｲｯﾁﾝｸﾞ電源":
                case "力率改善ﾕﾆｯﾄ":
                case "直流電源装置":
                case "AC/DCｲﾝﾊﾞｰﾀ":
                case "TDKﾗﾑﾀﾞ":
                case "電源ﾕﾆｯﾄ":
                    h.name = "電源";
                    break;
                case "ﾚﾊﾞｰｼﾌﾞﾙﾓｰﾀｰ":
                    h.name = "ﾚﾊﾞｰｼﾌﾞﾙﾓｰﾀ";
                    break;
                case "ｺﾝﾄﾛｰﾙﾎﾞｯｸｽ":
                    h.name = "ｺﾝﾄﾛｰﾗｰ";
                    break;
                case "直線運動型可変抵抗器":
                    h.name = "可変抵抗";
                    break;
                case "ﾊｲﾊﾟﾜｰﾘﾚｰ":
                case "ﾀｲﾑﾘﾚｰ":
                case "ｿﾘｯﾄﾞｽﾃｰﾄﾘﾚｰ":
                    h.name = "ﾘﾚｰ";
                    break;
                case "ﾘﾐｯﾄｽｲｯﾁ":
                case "小型ﾘﾐｯﾄｽｲｯﾁ":
                    h.name = "ﾏｲｸﾛｽｲｯﾁ";
                    break;
                case "電源置き換え金具":
                    h.name = "電源金具";
                    break;
                case "ファン":
                    h.name = "ﾌｧﾝ";
                    break;
                case "キセノン灯":
                    h.name = "ｷｾﾉﾝ灯";
                    break;
                }
                switch (h.name)
                {
                case "電源":
                    switch (h.maker)
                    {
                    case "TDK":
                        h.maker = "TDKﾗﾑﾀﾞ";
                        break;
                    }
                    break;
                }
                switch (h.model)
                {
                case "MVME162PA-252SE":
                    h.maker = "日本ﾓﾄﾛｰﾗ";
                    break;
                case "RTW05-60RF":
                case "RTW03-70RF":
                case "RTW04-60RF":
                case "MSE179":
                case "MSE179B":
                case "MSE179C":
                case "MSE179D":
                case "MSE179E":
                    h.maker = "TDKﾗﾑﾀﾞ";
                    break;
                case "NLU2F285DB":
                    h.maker = "日亜化学";
                    break;
                case "TC-38":
                case "TC-64J":
                    h.maker = "NECﾏｸﾞﾅｽ";
                    break;
                case "PB REC":
                    h.model = "PB-REC";
                    break;
                case "Center COM 445V2":
                case "CentreCom445 V2":
                    h.model = "CentreCom 445 V2";
                    break;
                case "CentreCom 275 V2":
                case "Center COM275 V2":
                    h.model = "CenterCOM 275 V2";
                    break;
                case "24V 1.5W":
                    h.model = "24V1.5W";
                    break;
                case "PBA1000F-5":
                case "R100-5-N":
                    h.name = "電源";
                    h.maker = "ｺｰｾﾙ";
                    break;
                case "CentreCOM 440":
                case "CenterCOM446":
                    h.name = "ﾄﾗﾝｼｰﾊﾞ";
                    h.model = "CentreCOM 440";
                    break;
                case "CentreCOM 2985":
                case "Center COM2985":
                    h.name = "ﾛｰｶﾙﾌﾞﾘｯｼﾞ";
                    h.model = "CentreCOM 2985";
                    break;
                case "PB-729":
                    h.name = "基板";
                    break;
                case "K15A-24N":
                    h.model = "K15A-24-N";
                    break;
                case "MYLP01Y10-R02":
                    h.maker = "NEW";
                    break;
                case "MC-50B":
                    h.maker = "ﾐﾈﾍﾞｱ";
                    break;
                case "NS10-203F（ロ）":
                    h.name = "蛍光灯器具";
                    h.model = "NS10-203F(ﾛ)";
                    break;
                case "SSS":
                    h.model = "SUPER-SS";
                    break;
                case "NY9400DRR":
                    h.maker = "日恵製作所";
                    break;
                case "MMC100A-1N":
                    h.model = "MMC100A-1-N";
                    break;
                case "UAW250S2.6-XKIT":
                    h.model = "UAW250S-2.6-XKIT";
                    break;
                case "UAW250S2.9-XKIT":
                    h.model = "UAW250S-2.9-XKIT";
                    break;
                case "UAW250S3.6-XKIT":
                    h.model = "UAW250S-3.6-XKIT";
                    break;
                case "R150-7-XNGY":
                case "MMC50A-1-N":
                    h.maker = "ｺｰｾﾙ";
                    break;
                case "PB-826 -12V":
                    h.model = "PA-826-12V";
                    break;
                case "ZWS100BAF-24/R":
                case "ZWS150BAF-5/R":
                    h.name = "電源";
                    h.maker = "TDKﾗﾑﾀﾞ";
                    break;
                case "PRE16B-RYG-F3MH":
                    h.name = "LED表示器";
                    break;
                }
                switch (h.maker)
                {
                case "ﾃﾞﾝｾｲ､ﾗﾑﾀﾞ":
                case "ﾃﾞﾝｾｲ・ﾗﾑﾀﾞ":
                case "TDK・ﾗﾑﾀﾞ":
                case "ﾃﾞﾝｾｲﾗﾑﾀﾞ":
                case "TDK､ﾗﾑﾀﾞ":
                    h.maker = "TDKﾗﾑﾀﾞ";
                    break;
                case "ｼｬｰﾌﾟ㈱":
                    h.maker = "ｼｬｰﾌﾟ";
                    break;
                case "松下電器":
                case "松下":
                    h.maker = "ﾊﾟﾅｿﾆｯｸ";
                    break;
                case "日本電気":
                    h.maker = "NEC";
                    break;
                case "橘ﾃｸﾄﾛﾝ":
                    h.maker = "ELSENA";
                    break;
                case "山洋電気":
                    h.maker = "三洋電気";
                    break;
                case "Vaisara":
                    h.maker = "ｳﾞｧｲｻﾗ";
                    break;
                case "アジア電子":
                    h.maker = "ｱｼﾞｱ電子";
                    break;
                case "七星科学研究所":
                case "七星":
                    h.maker = "七星科学";
                    break;
                case "日亜科学":
                    h.maker = "日亜化学";
                    break;
                }
                h.sheet_name = r.sheet_name;
                h.sheet_row = r.sheet_row;
                if (this.hins.ContainsKey(h.id))
                {
                    var hh = this.hins[r.F];
                    if (h.id != hh.id || h.name != hh.name || h.model != hh.model || h.maker != hh.maker)
                    {
                        hh.errs.Add(h);
                    }
                }
                else
                {
                    this.hins.Add(h.id, h);
                }
            }
            foreach (var h in this.hins.Values)
            {
                if (0 < h.errs.Count)
                {
                    /*
                    Trace.WriteLine($"------------------------差異があります------------------------");
                    Trace.WriteLine($"-----[{h.id}, {h.name}, {h.model}, {h.maker}] <- [{h.sheet_name}]の{h.sheet_row}行目");
                    foreach (var hh in h.errs)
                    {
                        Trace.WriteLine($"-----[{hh.id}, {hh.name}, {hh.model}, {hh.maker}] <- [{hh.sheet_name}]の{hh.sheet_row}行目");
                    }
                    */
                }
            }
            using (var conn = new NpgsqlConnection(connString))
            {
                conn.Open();
                foreach (var r in this.hins.Values)
                {
                    using (var cmd = new NpgsqlCommand(@"insert into zaiko.hinmokus(id, name, model, maker, original) values(:id, :name, :model, :maker, :original) RETURNING id;", conn))
                    {
                        NpgsqlParameter prm;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("id", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.id;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("name", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.name;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("model", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.model;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("maker", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.maker;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("original", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = $"{r.sheet_name}:{r.sheet_row}";
                        foreach (NpgsqlParameter p in cmd.Parameters)
                        {
                            if (p.Value == null) p.Value = DBNull.Value;
                        }
                        cmd.ExecuteScalar();
                    }
                }
                foreach (var r in this.recs)
                {
                    using (var cmd = new NpgsqlCommand(@"insert into zaiko.zaikos(basho, basho_no, basho_tana, hinmoku_id, tanka, kakaku, shuko, nyuko, model_v, model_kind, dengen_in, dengen_out, biko, seizo_date, zaiko_tekisei, zaiko_tuki, zaiko_new, zaiko_suu, zaiko_zan, scaw_flg, jigyosyo_id, scaw_fzcd, scaw_fhokancd, original) values(:basho, :basho_no, :basho_tana, :code, :tanka, :kakaku, :shuko, :nyuko, :model_v, :model_kind, :dengen_in, :dengen_out, :biko, :seizo_date, :zaiko_tekisei, :zaiko_tuki, :zaiko_new, :zaiko_suu, :zaiko_zan, :scaw_flg, :jigyosyo_id, :scaw_fzcd, :scaw_fhokancd, :original) RETURNING id;", conn))
                    {
                        NpgsqlParameter prm;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("basho", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.A;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("basho_no", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.B;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("basho_tana", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.C;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("code", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.F;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("tanka", NpgsqlTypes.NpgsqlDbType.Integer));
                        prm.Value = r.H;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("kakaku", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.I;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("shuko", NpgsqlTypes.NpgsqlDbType.Integer));
                        prm.Value = r.J;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("nyuko", NpgsqlTypes.NpgsqlDbType.Integer));
                        prm.Value = r.K;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("model_v", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.M;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("model_kind", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.N;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("dengen_in", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.O;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("dengen_out", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.P;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("biko", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.Q;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("seizo_date", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.T;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("zaiko_tekisei", NpgsqlTypes.NpgsqlDbType.Integer));
                        prm.Value = r.U;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("zaiko_tuki", NpgsqlTypes.NpgsqlDbType.Integer));
                        prm.Value = r.V;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("zaiko_new", NpgsqlTypes.NpgsqlDbType.Integer));
                        prm.Value = r.W;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("zaiko_suu", NpgsqlTypes.NpgsqlDbType.Integer));
                        //prm.Value = r.X;
                        prm.Value = r.Y;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("zaiko_zan", NpgsqlTypes.NpgsqlDbType.Integer));
                        prm.Value = r.Y;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("scaw_flg", NpgsqlTypes.NpgsqlDbType.Integer));
                        if (r.I == null)
                        {
                            prm.Value = 0;
                        }
                        else if (r.I.ToUpper() == "SCAW")
                        {
                            prm.Value = 1;
                        }
                        else
                        {
                            prm.Value = 0;
                        }
                        prm = cmd.Parameters.Add(new NpgsqlParameter("jigyosyo_id", NpgsqlTypes.NpgsqlDbType.Integer));
                        prm.Value = r.jigyosyo_id;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("scaw_fzcd", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.D;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("scaw_fhokancd", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = r.E;
                        prm = cmd.Parameters.Add(new NpgsqlParameter("original", NpgsqlTypes.NpgsqlDbType.Text));
                        prm.Value = $"{r.sheet_name}:{r.sheet_row}";
                        foreach (NpgsqlParameter p in cmd.Parameters)
                        {
                            if (p.Value == null) p.Value = DBNull.Value;
                        }
                        cmd.ExecuteScalar();
                    }
                }
            }
        }
    }
}
