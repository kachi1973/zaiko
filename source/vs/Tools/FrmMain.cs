using System;
using System.Collections;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Printing;
using System.Text;
using System.Text.Json;
using System.Threading.Tasks;
using System.Windows.Forms;
using QRCoder;

namespace Tools
{
	public partial class FrmMain : Form
	{
#if LBL65150
		private float margin_x = 13.5f;
		private float margin_y = 12.5f;
		private float waku_w = 28f;
		private float waku_h = 8f;
		private float int_x = 3f;
		private float int_y = 3f;
		private int num_x = 6;
		private int num_y = 25;
#endif
#if LBL72200
		private float margin_x = 14.5f;
		private float margin_y = 12.5f;
		private float waku_w = 20f;
		private float waku_h = 8f;
		private float int_x = 3f;
		private float int_y = 3f;
		private int num_x = 8;
		private int num_y = 25;
		private float font_size = 6f;
#endif
		private List<Lib.Lib.Kzaiko> kzaikos = new List<Lib.Lib.Kzaiko>();
		private Queue<Lib.Lib.Kzaiko> prints = new Queue<Lib.Lib.Kzaiko>();
		private decimal TxtXValue = 1;
		private decimal TxtYValue = 1;
		public FrmMain()
		{
			InitializeComponent();

			var assm = System.Reflection.Assembly.GetExecutingAssembly();
			var name = assm.GetName();
			this.Text = $"{this.Text}({name.Version})";

			this.TxtX.Minimum = 1;
			this.TxtX.Maximum = Tools.Properties.Settings.Default.num_x;
			this.TxtY.Minimum = 1;
			this.TxtY.Maximum = Tools.Properties.Settings.Default.num_y;
			try
			{
				this.TxtX.Value = Tools.Properties.Settings.Default.cell_x;
				this.TxtY.Value = Tools.Properties.Settings.Default.cell_y;
			}
			catch
			{
			}
			this.ListMain.CheckBoxes = true;
			this.ListMain.Columns.Add("ID");
			this.ListMain.Columns.Add("在庫ID");
			this.ListMain.Columns.Add("作成日");
			this.ListMain.Columns.Add("貯蔵品コード");
			this.ListMain.Columns.Add("型式");
			this.ListMain.Columns.Add("Ver");
			this.ListMain.Columns.Add("種別");
			this.ListMain.Columns.Add("備考");
			this.ListMain.Columns.Add("名称");
			this.ListMain.Columns.Add("メーカ");
			this.ListMain.Columns.Add("製造年月");

			string[] cmds = System.Environment.GetCommandLineArgs();
			if (1 < cmds.Length)
			{
				this.open(cmds[1]);
			}
		}
		private void CmdPrint_Click(object sender, EventArgs e)
		{
			if (this.kzaikos != null && 0 < this.kzaikos.Count)
			{
				using (var dlg = new PrintPreviewDialog())
				{
					var pd = new System.Drawing.Printing.PrintDocument();
					pd.BeginPrint += Pd_BeginPrint;
					pd.PrintPage += Pd_PrintPage;
					var pdl = new PrintDialog();
					pdl.Document = pd;
					if (pdl.ShowDialog() == DialogResult.OK)
					{
						dlg.Document = pd;
						this.TxtXValue = this.TxtX.Value;
						this.TxtYValue = this.TxtY.Value;
						dlg.ShowDialog();
						this.TxtX.Value = this.TxtXValue;
						this.TxtY.Value = this.TxtYValue;
					}
				}
			}
		}

		private void Pd_BeginPrint(object sender, System.Drawing.Printing.PrintEventArgs e)
		{
			this.prints.Clear();
			for (var x = 1; x < this.TxtX.Value; ++x)
			{
				this.prints.Enqueue(null);
			}
			for (var y = 1; y < this.TxtY.Value; ++y)
			{
				for (var x = 0; x < Tools.Properties.Settings.Default.num_x; ++x)
				{
					this.prints.Enqueue(null);
				}
			}
			for (var idx = 0; idx < this.kzaikos.Count; ++idx)
			{
				if (this.ListMain.Items[idx].Checked)
				{
					this.prints.Enqueue(this.kzaikos[idx]);
				}
			}
		}
		private void Pd_PrintPage(object sender, System.Drawing.Printing.PrintPageEventArgs e)
		{
			var pd = sender as System.Drawing.Printing.PrintDocument;
			var g = e.Graphics;
			g.PageUnit = GraphicsUnit.Millimeter;
			var item_idx = 0;
			using (var font = new Font("ＭＳ ゴシック", Tools.Properties.Settings.Default.font_size, FontStyle.Regular))
			using (var pen = new Pen(Color.Black, 0.1f))
			{
				while (0 < this.prints.Count)
				{
					var cnt_y = item_idx / Tools.Properties.Settings.Default.num_x;
					if (!(cnt_y < Tools.Properties.Settings.Default.num_y)) break;
					var cnt_x = item_idx % Tools.Properties.Settings.Default.num_x;
					var item = this.prints.Dequeue();
					if (item != null)
					{
						var qr = new QRCodeGenerator();
						var qrData = qr.CreateQrCode($"{item.id}", QRCodeGenerator.ECCLevel.Q);
						var qrCode = new QRCode(qrData);
						var bmp = qrCode.GetGraphic(1, Color.Black, Color.White, true);
						var y = Tools.Properties.Settings.Default.margin_y + ((Tools.Properties.Settings.Default.waku_h + Tools.Properties.Settings.Default.int_y) * cnt_y);
						var x = Tools.Properties.Settings.Default.margin_x + ((Tools.Properties.Settings.Default.waku_w + Tools.Properties.Settings.Default.int_x) * cnt_x);
						var rct = new RectangleF(x, y, Tools.Properties.Settings.Default.waku_w, Tools.Properties.Settings.Default.waku_h);
						//g.DrawRectangle(pen, rct.X, rct.Y, rct.Width, rct.Height);
						g.DrawImage(bmp, rct.X, rct.Y, rct.Height, rct.Height);
						var sf = new StringFormat();
						const int row_max = 4;
						var rct2 = rct;
						rct2.Inflate(0, -0.5f);
						for (var row_idx = 0; row_idx < row_max; ++row_idx)
						{
							var rct3 = new RectangleF(rct2.X + rct.Height, rct2.Y + (rct2.Height / row_max * row_idx), rct2.Width - rct2.Height, rct2.Height / row_max);
							switch (row_idx)
							{
								case 0:
									g.DrawString($"{item.id}", font, Brushes.Black, rct3);
									break;
								case 1:
									g.DrawString($"{item.hinmoku_id}", font, Brushes.Black, rct3);
									break;
								case 2:
									if (item.hinmoku != null) g.DrawString($"{item.hinmoku.name}", font, Brushes.Black, rct3);
									break;
								case 3:
									if (item.hinmoku != null) g.DrawString($"{item.hinmoku.model}{item.model_v}{item.model_kind}", font, Brushes.Black, rct3);
									break;
							}
						}
					}
					item_idx++;
					if (!pd.PrintController.IsPreview)
					{
						var idx = item_idx % (Tools.Properties.Settings.Default.num_x * Tools.Properties.Settings.Default.num_y);
						this.TxtYValue = (idx / Tools.Properties.Settings.Default.num_x) + 1;
						this.TxtXValue = (idx % Tools.Properties.Settings.Default.num_x) + 1;
					}
				}
			}
			e.HasMorePages = 0 < this.prints.Count;
		}
		private void CmdOpen_Click(object sender, EventArgs e)
		{
			using (var dlg = new OpenFileDialog())
			{
				dlg.Filter = "kzqr files|*.kzqr";
				dlg.RestoreDirectory = true;
				dlg.CheckFileExists = true;
				if (dlg.ShowDialog() == DialogResult.OK)
				{
					this.open(dlg.FileName);
				}
			}
		}
		private void open(string path)
		{
			var json_str = System.IO.File.ReadAllText(path, System.Text.Encoding.UTF8);
			var opt = new JsonSerializerOptions();
			var datas = JsonSerializer.Deserialize<Lib.Lib.Kzaiko[]>(json_str, opt);
			foreach (var data in datas)
			{
				if (!this.kzaikos.Exists(kz => kz.id == data.id))
				{
					this.kzaikos.Add(data);
				}
			}
			this.ListMain.Items.Clear();
			foreach (var item in this.kzaikos)
			{
				var lvi = this.ListMain.Items.Add($"{item.id}");
				lvi.Checked = true;
				lvi.SubItems.Add($"{item.zaiko_id}");
				lvi.SubItems.Add($"{item.created_at?.ToString("yyyy/MM/dd")}");
				lvi.SubItems.Add(item.hinmoku_id);
				if (item.hinmoku != null)
				{
					lvi.SubItems.Add(item.hinmoku.model);
				}
				else
				{
					lvi.SubItems.Add("");
				}
				lvi.SubItems.Add(item.model_v);
				lvi.SubItems.Add(item.model_kind);
				lvi.SubItems.Add(item.biko);
				if (item.hinmoku != null)
				{
					lvi.SubItems.Add(item.hinmoku.name);
					lvi.SubItems.Add(item.hinmoku.maker);
				}
				else
				{
					lvi.SubItems.Add("");
					lvi.SubItems.Add("");
				}
				lvi.SubItems.Add(item.seizo_date);
			}
			this.ListMain.AutoResizeColumns(ColumnHeaderAutoResizeStyle.HeaderSize);
		}

		private void CmdClear_Click(object sender, EventArgs e)
		{
			this.kzaikos.Clear();
			this.ListMain.Items.Clear();
		}

		private void FrmMain_FormClosed(object sender, FormClosedEventArgs e)
		{
			Tools.Properties.Settings.Default.cell_x = (int)this.TxtX.Value;
			Tools.Properties.Settings.Default.cell_y = (int)this.TxtY.Value;
			Tools.Properties.Settings.Default.Save();
		}

		private void CmdSettings_Click(object sender, EventArgs e)
		{
			using (var dlg = new FrmSettings())
			{
				dlg.ShowDialog();
			}
		}

		private void CmdAllSel_Click(object sender, EventArgs e)
		{
			foreach(ListViewItem item in this.ListMain.Items)
			{
				item.Checked = true;
			}
		}

		private void CmdAllAnsel_Click(object sender, EventArgs e)
		{
			foreach (ListViewItem item in this.ListMain.Items)
			{
				item.Checked = false;
			}
		}
	}
}