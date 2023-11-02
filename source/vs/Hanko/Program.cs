using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Drawing;
using System.Drawing.Drawing2D;

namespace Hanko
{
    class Options
    {
        [CommandLine.Option('n', "name", Required = true, HelpText = "name")]
        public string name
        {
            get;
            set;
        }
        [CommandLine.Option('d', "date", Required = false, HelpText = "date", Default = null)]
        public DateTime date
        {
            get;
            set;
        }
        [CommandLine.Option('w', "width", Required = false, HelpText = "width", Default = 128)]
        public int width
        {
            get;
            set;
        }
    }
    public class Program
    {
        static void Main(string[] args)
        {
            CommandLine.ParserResult<Options> result = CommandLine.Parser.Default.ParseArguments<Options>(args);
            if (result.Tag == CommandLine.ParserResultType.Parsed)
            {
                var opts = (CommandLine.Parsed<Options>)result;
                using (var st = Console.OpenStandardOutput())
                using (var bmp = Image.Hanko(opts.Value.name.Replace(@"""", ""), opts.Value.date, Hanko.Properties.Settings.Default.Names.Split(','), opts.Value.width))
                {
                    // 出力
                    bmp.Save(st, System.Drawing.Imaging.ImageFormat.Png);
                    st.Close();
                }
            }
            else
            {
                var notParsed = (CommandLine.NotParsed<Options>)result;
                System.Console.WriteLine("invalid options");
            }
        }
        public static string[] Names
        {
            get
            {
                return Hanko.Properties.Settings.Default.Names.Split(',');
            }
        }
    }
}
