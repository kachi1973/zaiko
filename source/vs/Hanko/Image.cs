using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Drawing;
using System.Drawing.Drawing2D;

namespace Hanko
{
    public class Image
    {
        public static Bitmap Hanko(string name_org, DateTime? date, string[] names, int width)
        {
            var len = 2;
            var name1 = String.Empty;
            var name2 = String.Empty;
            if (name_org.Contains(" "))
            {
                len = Math.Min(name_org.IndexOf(" "), 3);
                name_org = name_org.Replace(" ", "");
            }
            else if (6 <= name_org.Length)
            {
                len = 3;
            }
            else
            {
                for (var idx = 0; idx < names.Length; idx += 2)
                {
                    if (name_org.Contains(names[idx]))
                    {
                        len = int.Parse(names[idx + 1]);
                        break;
                    }
                }
            }
            if (len <= name_org.Length)
            {
                name1 = name_org.Substring(0, len);
                if (0 < (name_org.Length - len))
                {
                    name2 = name_org.Substring(len, Math.Min(name_org.Length - len, 3));
                }
            }
            else if (0 < len)
            {
                name1 = name_org;
            }
            var bmp = new Bitmap(width, width);
            using (var g = Graphics.FromImage(bmp))
            using (var pen = new Pen(Color.Red, 2))
            using (var fnt1 = new Font("ＭＳ Ｐ明朝", 20, FontStyle.Bold))
            {
                g.SmoothingMode = SmoothingMode.HighQuality;
                g.TextRenderingHint = System.Drawing.Text.TextRenderingHint.AntiAlias;
                var base_rct = new RectangleF(1, 1, bmp.Width - 3, bmp.Height - 3);
                var offset = 3;
                // 苗字
                g.DrawEllipse(pen, base_rct);
                var fmt = new StringFormat(StringFormatFlags.NoClip);
                fmt.Alignment = StringAlignment.Center;
                fmt.LineAlignment = StringAlignment.Far;
                var rct = new RectangleF(base_rct.X, base_rct.Y, base_rct.Width, base_rct.Height * 0.37f);
                g.DrawString(name1, fnt1, Brushes.Red, rct, fmt);
                // 日付
                rct.Y += rct.Height;
                rct.Height = base_rct.Height * 0.26f;
                fmt.LineAlignment = StringAlignment.Center;
                if (date.HasValue && DateTime.MinValue < date)
                {
                    g.DrawString(date.Value.ToString("yy/MM/dd"), fnt1, Brushes.Red, rct, fmt);
                }
                g.DrawLine(pen, rct.X + offset, rct.Y, rct.X + rct.Width - offset, rct.Y);
                // 名前
                rct.Y += rct.Height;
                rct.Height = base_rct.Height * 0.37f;
                fmt.LineAlignment = StringAlignment.Center;
                g.DrawString(name2, fnt1, Brushes.Red, rct, fmt);
                g.DrawLine(pen, rct.X + offset, rct.Y, rct.X + rct.Width - offset, rct.Y);
                return bmp;
            }
        }
    }
}
