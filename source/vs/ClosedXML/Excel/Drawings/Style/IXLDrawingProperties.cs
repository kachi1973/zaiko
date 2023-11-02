using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace ClosedXML.Excel
{
    public interface IXLDrawingProperties
    {
        XLDrawingAnchor Positioning { get; set; }
        IXLDrawingStyle SetPositioning(XLDrawingAnchor value);

    }
}
