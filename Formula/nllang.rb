class Nllang < Formula
  desc "A Dutch-inspired toy programming language"
  homepage "https://github.com/aagjalpankaj/nllang"
  url "https://github.com/aagjalpankaj/nllang/releases/download/v0.1.0/nllang.phar"
  sha256 "4e902fe323615f495e2ed15573e4f401d376ff1fde361dab8e0fe40b4e9b721d"
  version "0.1.0"

  depends_on "php"

  def install
    libexec.install "nllang.phar"
    (bin/"nllang").write <<~SH
      #!/bin/bash
      exec php "#{libexec}/nllang.phar" "$@"
    SH
  end

  test do
    (testpath/"hallo.nl").write('hoi zeg "Hallo, wereld!"; doei')
    assert_equal "Hallo, wereld!", shell_output("#{bin}/nllang hallo.nl").strip
  end
end
