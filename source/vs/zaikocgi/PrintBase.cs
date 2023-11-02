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

/*
new iTextSharp.text.Rectangle(llx, lly, urx, ury);
new iTextSharp.text.Rectangle(左下x, 左下y, 右上x, 右上y);

+y
A
|          TOP
|     +------------B
|     |            |
|    L|            |R
|     |            |
|     A------------+
|         BOTTOM
0-------------------------> +x
A = (llx, lly)  B = (urx, ury)
(lower left X, lower left Y) - (upper right X, upper right Y)
単位はポイント pt です。1pt ≒ 0.352777mm 参考
A4 の場合、約 595x842 pt ⇔ 210x297 mm
*/

namespace zaikocgi
{
    public static class RectangleEx
    {
        public static Rectangle NextRow(this Rectangle r)
        {
            var h = r.Height;
            r.Top -= h;
            r.Bottom -= h;
            return r;
        }
        public static Rectangle NextCol(this Rectangle r)
        {
            var w = r.Width;
            r.Left += w;
            r.Right += w;
            return r;
        }
        public static Rectangle SetWidth(this Rectangle r, float w)
        {
            r.Right = r.Left + w;
            return r;
        }
    }
    class PrintBase
    {
        protected Font fntL = null;
        protected Font fntM = null;
        protected Font fntM_red = null;
		protected Font fntS = null;
		protected float row_height = 30f;
		protected float cell_padding = 1f;
		public PrintBase()
        {
            var bf = BaseFont.CreateFont(Path.Combine(Environment.SystemDirectory.Replace("system32", "fonts"), "msmincho.ttc,1"), BaseFont.IDENTITY_H, BaseFont.EMBEDDED);
            fntL = new Font(bf, 18f);
            fntM = new Font(bf, 9f);
            fntM_red = new Font(bf, 10f, fntM.Style, BaseColor.RED);
			fntS = new Font(bf, 8f);
		}
		public string date_str(DateTime? date)
        {
            return date.HasValue ? get_date(date).Value.ToString("yyyy/MM/dd") : "          ";
        }
        public DateTime? get_date(DateTime? date)
        {
            if (!date.HasValue) return null;
            return date.Value.ToLocalTime();
        }
        protected void set_object(PdfContentByte cb, Rectangle rct, object text, float leading, int alignment, bool is_image, Font fnt, bool line, float text_top_offset = -2.2f)
        {
            if (line)
            {
                cb.SetRGBColorStroke(0, 0, 0);
                cb.Rectangle(rct.Left, rct.Bottom, rct.Width, rct.Height);
                cb.Stroke();
            }
            else
            {
#if false
                cb.SetRGBColorStroke(240, 240, 240);
                cb.Rectangle(rct.Left, rct.Bottom, rct.Width, rct.Height);
                cb.Stroke();
#endif
            }
            if (text != null)
            {
                if (!is_image)
                {
                    var ct = new ColumnText(cb);
                    var chunk = new Chunk(text.ToString(), fnt);
                    var phase = new Phrase(chunk);
                    ct.SetSimpleColumn(phase, rct.Left + 2, rct.Top + text_top_offset, rct.Right - 5, rct.Bottom, leading, alignment);
                    ct.Go();
                }
                else
                {
                    var image = iTextSharp.text.Image.GetInstance(new Uri(text.ToString()));
                    image.SetAbsolutePosition(rct.Left, rct.Bottom);
                    image.ScaleAbsolute(rct.Width, rct.Height);
                    cb.AddImage(image);
                }
            }
        }
        protected void set_object(PdfContentByte cb, Rectangle rct, object text, float leading, int alignment)
        {
            set_object(cb, rct, text, leading, alignment, false, this.fntM, false);
        }
        protected void set_object(PdfContentByte cb, Rectangle rct, object text, int alignment, Font fnt, bool line)
        {
            set_object(cb, rct, text, fntM.Size, alignment, false, fnt, line);
        }
        protected void set_object(PdfContentByte cb, Rectangle rct, object text)
        {
            set_object(cb, rct, text, fntM.Size, Element.ALIGN_LEFT, false, this.fntM, false);
        }
        protected void set_img(PdfContentByte cb, Rectangle rct, object text)
        {
            set_object(cb, rct, text, fntM.Size, Element.ALIGN_CENTER, true, this.fntM, false);
        }
        public PdfPCell get_cell()
        {
            return get_cell("");
        }
        public PdfPCell get_cell<T>(T text)
        {
            return get_cell(text, false, 0);
        }
        public PdfPCell get_cell<T>(T text, bool header)
        {
            return get_cell(text, header, header ? 1 : 0);
        }
        public PdfPCell get_cell<T>(T text, bool header, int colspan)
        {
            return get_cell(text, header, colspan, 1, header ? 1 : 0, this.fntM);
        }
        public PdfPCell get_cell<T>(T text, bool header, int colspan, int hali)
        {
            return get_cell(text, header, colspan, 1, hali, this.fntM);
        }
        public PdfPCell get_cell<T>(T text, bool header, int colspan, int rowspan, int hali, Font fnt)
        {
            return get_cell(text, header, colspan, rowspan, hali, fnt, "");
        }
        public PdfPCell get_cell<T>(T text, bool header, int colspan, int rowspan, int hali, Font fnt, string suffix)
        {
            PdfPCell cell = null;
            if (text != null)
            {
                if (typeof(T) == typeof(DateTime?))
                {
                    cell = new PdfPCell(new Phrase((text as DateTime?).Value.ToString("yyyy/MM/dd"), fnt));
                }
                else if (typeof(T) == typeof(System.Drawing.Bitmap))
                {
                    using (var img_org = text as System.Drawing.Image)
                    {
                        //var img = Image.GetInstance(img_org, BaseColor.WHITE);
                        var img = Image.GetInstance(img_org, System.Drawing.Imaging.ImageFormat.Png);
                        img.ScaleToFit(50, 50);
                        cell = new PdfPCell(img);
                        cell.Padding = 5f;
                    }
                }
                else if (typeof(T) == typeof(int?))
                {
                    var num = text as int?;
                    cell = new PdfPCell(new Phrase(num.Value.ToString("#,0") + suffix, fnt));
                }
                else if (typeof(T) == typeof(ulong?))
                {
                    var num = text as ulong?;
                    cell = new PdfPCell(new Phrase(num.Value.ToString("#,0") + suffix, fnt));
                }
                else
                {
                    cell = new PdfPCell(new Phrase(text.ToString(), fnt));
                }
            }
            else
            {
                cell = new PdfPCell();
            }
            cell.FixedHeight = row_height;
            cell.Colspan = colspan;
            cell.Rowspan = rowspan;
            cell.HorizontalAlignment = hali; //0=Left, 1=Centre, 2=Right
            cell.VerticalAlignment = Element.ALIGN_MIDDLE;
            cell.PaddingTop = this.cell_padding;
            cell.PaddingBottom = this.cell_padding;
            cell.PaddingLeft = this.cell_padding;
            cell.PaddingRight = this.cell_padding;
            if (header)
            {
                cell.BackgroundColor = BaseColor.LIGHT_GRAY;
            }
            return cell;
        }
    }
}
